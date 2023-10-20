<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Route::macro('client', function($url, $view) {
            return Route::get($url, fn() => view()->client($view));
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::macro('sensesApiResources', function (array $resources, array $options = []) {
            Route::apiResources($resources);

            foreach ($resources as $name => $controller) {
                $snakeName = Str::singular(str_replace('-', '_', $name));
                Route::match(['put', 'patch'], $name . '/{'.$snakeName.'}/lock', [$controller, 'lock'])->name($name . '.lock');
                Route::match(['put', 'patch'], $name . '/{'.$snakeName.'}/unlock', [$controller, 'unlock'])->name($name . '.unlock');
            }
        });
    }
}
