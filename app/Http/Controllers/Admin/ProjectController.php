<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Services\MediaStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProjectController extends Controller
{
    public function __construct(private MediaStorage $media) {}

    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::query()->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', ['project' => new Project([
            'media_type' => 'video',
            'video_orientation' => 'landscape',
            'status' => ProjectStatus::Draft,
        ])]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = new Project(['sort_order' => (int) Project::max('sort_order') + 1]);

        $this->save($project, $request);

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project created.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', ['project' => $project->load('media')]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $this->save($project, $request);

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project saved.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $paths = $project->media->pluck('path')->push($project->cover_path, $project->teaser_path)->filter();

        $project->delete();
        $paths->each(fn (string $path) => $this->media->delete($path));

        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    public function move(Project $project, string $direction): RedirectResponse
    {
        DB::transaction(function () use ($project, $direction): void {
            $ordered = Project::query()->ordered()->lockForUpdate()->get()->values();
            $index = $ordered->search(fn (Project $item): bool => $item->is($project));
            $swapWith = $direction === 'up' ? $index - 1 : $index + 1;

            if ($index === false || ! $ordered->has($swapWith)) {
                return;
            }

            $items = $ordered->all();
            [$items[$index], $items[$swapWith]] = [$items[$swapWith], $items[$index]];

            foreach ($items as $position => $item) {
                $item->forceFill(['sort_order' => $position + 1])->saveQuietly();
            }
        });

        return back()->with('status', 'Order updated.');
    }

    public function toggle(Project $project, string $field): RedirectResponse
    {
        if ($field === 'featured') {
            $project->update(['is_featured' => ! $project->is_featured]);
        } else {
            $publishing = $project->status !== ProjectStatus::Published;
            $project->update([
                'status' => $publishing ? ProjectStatus::Published : ProjectStatus::Draft,
                'published_at' => $publishing ? ($project->published_at ?? now()) : $project->published_at,
            ]);
        }

        return back()->with('status', 'Project updated.');
    }

    public function destroyMedia(Project $project, ProjectMedia $media): RedirectResponse
    {
        $path = $media->path;
        $media->delete();
        $this->media->delete($path);

        return back()->with('status', 'Image removed.');
    }

    /**
     * Stores any uploads, then writes the record in a transaction. Newly
     * stored files are removed again if the database write fails, and
     * replaced files are only deleted once the write has succeeded.
     */
    private function save(Project $project, ProjectRequest $request): void
    {
        $data = $request->safe()->except(['cover', 'remove_cover', 'teaser', 'remove_teaser', 'gallery', 'gallery_alt', 'media', 'youtube_url']);
        $data['youtube_id'] = $request->youtubeId();

        if ($data['status'] === ProjectStatus::Published->value && ! $project->published_at) {
            $data['published_at'] = now();
        }

        $newFiles = [];
        $obsolete = [];

        try {
            if ($request->hasFile('cover')) {
                $cover = $this->media->storeImage($request->file('cover'), 'projects');
                $newFiles[] = $cover['path'];
                $obsolete[] = $project->cover_path;
                $data += ['cover_path' => $cover['path'], 'cover_width' => $cover['width'], 'cover_height' => $cover['height']];
            } elseif ($request->boolean('remove_cover')) {
                $obsolete[] = $project->cover_path;
                $data += ['cover_path' => null, 'cover_width' => null, 'cover_height' => null, 'cover_alt' => null];
            }

            if ($request->hasFile('teaser')) {
                $data['teaser_path'] = $newFiles[] = $this->media->storeTeaser($request->file('teaser'));
                $obsolete[] = $project->teaser_path;
            } elseif ($request->boolean('remove_teaser')) {
                $obsolete[] = $project->teaser_path;
                $data['teaser_path'] = null;
            }

            $gallery = [];
            foreach ($request->file('gallery', []) as $index => $file) {
                $stored = $this->media->storeImage($file, 'projects/gallery');
                $newFiles[] = $stored['path'];
                $gallery[] = $stored + ['alt' => (string) $request->input("gallery_alt.$index")];
            }

            DB::transaction(function () use ($project, $data, $gallery, $request): void {
                $project->fill($data)->save();

                foreach ((array) $request->validated('media', []) as $id => $fields) {
                    $project->media()->whereKey($id)->update([
                        'alt' => $fields['alt'],
                        'caption' => $fields['caption'] ?? null,
                        'sort_order' => (int) ($fields['sort_order'] ?? 0),
                    ]);
                }

                $nextOrder = (int) $project->media()->max('sort_order');
                foreach ($gallery as $item) {
                    $project->media()->create($item + ['sort_order' => ++$nextOrder]);
                }
            });
        } catch (Throwable $exception) {
            array_map(fn (string $path) => $this->media->delete($path), $newFiles);

            throw $exception;
        }

        array_map(fn (?string $path) => $this->media->delete($path), $obsolete);
    }
}
