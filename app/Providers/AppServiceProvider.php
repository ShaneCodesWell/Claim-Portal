<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        View::composer('*', function ($view) {
            $view->with('company', cache()->remember('company', 3600, fn() => Company::first()));
        });

        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            // Class-qualified so Customer #5 and User #5 don't share a bucket
            return Limit::perMinute(60)->by($user ? get_class($user) . ':' . $user->getAuthIdentifier() : $request->ip());
        });

        RateLimiter::for('api-auth', fn(Request $request) => Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('api-writes', function (Request $request) {
            $user = $request->user();

            return Limit::perMinute(20)->by($user ? get_class($user) . ':' . $user->getAuthIdentifier() : $request->ip());
        });
    }
}
