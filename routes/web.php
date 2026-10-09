<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/work', 'work')->name('work');
    Route::get('/contact', 'contact')->name('contact');
});

Route::post('/contact', [EnquiryController::class, 'store'])
    ->middleware('throttle:enquiries')
    ->name('enquiries.store');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');
    });

    Route::middleware(['auth', 'can:manage-site'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::resource('projects', Admin\ProjectController::class)->except('show');
        Route::post('projects/{project}/move/{direction}', [Admin\ProjectController::class, 'move'])
            ->whereIn('direction', ['up', 'down'])->name('projects.move');
        Route::patch('projects/{project}/toggle/{field}', [Admin\ProjectController::class, 'toggle'])
            ->whereIn('field', ['featured', 'published'])->name('projects.toggle');
        Route::delete('projects/{project}/media/{media}', [Admin\ProjectController::class, 'destroyMedia'])
            ->scopeBindings()->name('projects.media.destroy');

        Route::resource('services', Admin\ServiceController::class)->only(['index', 'edit', 'update']);
        Route::resource('packages', Admin\PackageController::class)->only(['index', 'edit', 'update']);
        Route::resource('team', Admin\TeamMemberController::class)->except('show')
            ->parameters(['team' => 'teamMember']);

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('copy/{page?}', [Admin\PageCopyController::class, 'edit'])->name('copy.edit');
        Route::put('copy/{page}', [Admin\PageCopyController::class, 'update'])->name('copy.update');

        Route::get('enquiries', [Admin\EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [Admin\EnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('enquiries/{enquiry}', [Admin\EnquiryController::class, 'update'])->name('enquiries.update');
    });
});
