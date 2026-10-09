<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\MediaStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class TeamMemberController extends Controller
{
    public function __construct(private MediaStorage $media) {}

    public function index(): View
    {
        return view('admin.team.index', ['members' => TeamMember::query()->orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('admin.team.form', ['member' => new TeamMember(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $member = new TeamMember(['sort_order' => (int) TeamMember::max('sort_order') + 1]);
        $this->save($request, $member);

        return redirect()->route('admin.team.index')->with('status', 'Team member added.');
    }

    public function edit(TeamMember $teamMember): View
    {
        return view('admin.team.form', ['member' => $teamMember]);
    }

    public function update(Request $request, TeamMember $teamMember): RedirectResponse
    {
        $this->save($request, $teamMember);

        return redirect()->route('admin.team.index')->with('status', 'Team member saved.');
    }

    public function destroy(TeamMember $teamMember): RedirectResponse
    {
        $path = $teamMember->photo_path;
        $teamMember->delete();
        $this->media->delete($path);

        return redirect()->route('admin.team.index')->with('status', 'Team member removed.');
    }

    private function save(Request $request, TeamMember $member): void
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:800'],
            'photo' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max((int) config('sparklenz.max_image_kb'))
                ->dimensions(Rule::dimensions()->minWidth(400)->minHeight(400)->maxWidth(8000)->maxHeight(8000))],
            'photo_alt' => ['nullable', 'string', 'max:250', Rule::requiredIf(fn (): bool => $request->hasFile('photo'))],
            'remove_photo' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $old = null;
        $data = [
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'photo_alt' => $validated['photo_alt'] ?? $member->photo_alt,
            'is_published' => $request->boolean('is_published'),
            'is_demo' => $request->boolean('is_demo'),
            'sort_order' => $validated['sort_order'] ?? $member->sort_order,
        ];

        if ($request->hasFile('photo')) {
            $old = $member->photo_path;
            $data['photo_path'] = $this->media->storeImage($request->file('photo'), 'team')['path'];
        } elseif ($request->boolean('remove_photo')) {
            $old = $member->photo_path;
            $data['photo_path'] = null;
        }

        $member->fill($data)->save();
        $this->media->delete($old);
    }
}
