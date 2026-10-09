<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Support\ProjectWall;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home(): View
    {
        $featured = Project::visible()->where('is_featured', true)->ordered()->with('media')->take(4)->get();

        if ($featured->isEmpty()) {
            $featured = Project::visible()->ordered()->with('media')->take(4)->get();
        }

        $allProjects = Project::visible()->ordered()->with('media')->get();

        return view('pages.home', [
            'projects' => $featured,
            'allProjects' => $allProjects,
            'wallColumns' => ProjectWall::columns($allProjects),
            'services' => Service::published()->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'team' => TeamMember::visible()->get(),
            'storyProject' => Project::visible()
                ->whereNotNull('cover_path')
                ->whereColumn('cover_width', '>', 'cover_height')
                ->orderBy('is_demo')
                ->ordered()
                ->first(),
        ]);
    }

    public function services(): View
    {
        return view('pages.services', [
            'services' => Service::published()->with('rates')->get(),
            'packages' => Package::published()->with('tiers')->get(),
        ]);
    }

    public function work(Request $request): View
    {
        /** @var Collection<int, Project> $projects */
        $projects = Project::visible()->ordered()->with('media')->get();

        return view('pages.work', [
            'projects' => $projects,
            'openSlug' => $projects->firstWhere('slug', $request->query('project'))?->slug,
        ]);
    }

    public function contact(Request $request): View
    {
        $services = Service::published()->pluck('name');
        $packages = Package::published()->pluck('name');

        $selectedService = $request->query('service');
        $selectedProject = Project::visible()->where('slug', $request->query('project'))->first();

        return view('pages.contact', [
            'interestOptions' => $services->merge($packages)->values(),
            'selectedInterest' => collect([$selectedService])->filter()
                ->map(fn (string $slug): ?string => Service::where('slug', $slug)->value('name') ?? Package::where('slug', $slug)->value('name'))
                ->filter()->first(),
            'selectedProject' => $selectedProject,
        ]);
    }
}
