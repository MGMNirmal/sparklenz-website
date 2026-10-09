<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnquiryStatus;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Project;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newEnquiries' => Enquiry::where('status', EnquiryStatus::New)->count(),
            'publishedProjects' => Project::where('status', ProjectStatus::Published)->count(),
            'draftProjects' => Project::where('status', ProjectStatus::Draft)->count(),
            'demoProjects' => Project::where('is_demo', true)->count(),
            'recentEnquiries' => Enquiry::latest()->take(5)->get(),
        ]);
    }
}
