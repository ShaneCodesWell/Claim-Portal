<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Support\ApiDocs;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;

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

        // The API docs page is open only where the sandbox flag is on (the test server), never on live
        Gate::define('viewApiDocs', fn(?\App\Models\User $user = null) => (bool) config('app.sandbox'));

        Scramble::configure()->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->info->title       = 'Vanguard eClaim API';
            $openApi->info->version     = '1.0.0';
            $openApi->info->description = ApiDocs::description();

            $openApi->secure(SecurityScheme::http('bearer'));
        });
    }
}
