<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-site', fn (User $user): bool => $user->is_admin === true);

        RateLimiter::for('enquiries', fn (Request $request): array => [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(10)->by($request->ip()),
        ]);

        RateLimiter::for('admin-login', fn (Request $request): Limit => Limit::perMinute(5)
            ->by(strtolower((string) $request->input('email')).'|'.$request->ip()));

        Password::defaults(fn () => Password::min(12)->letters()->numbers());
    }
}
