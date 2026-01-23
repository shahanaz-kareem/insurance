<?php

namespace App\Providers;

use App\Exceptions\InsuraModelNotFoundException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Route model bindings
        Route::bind('broker', function ($value) {
            try {
                return \App\Models\User::withStatus()->findOrFail($value);
            } catch (ModelNotFoundException $e) {
                throw new InsuraModelNotFoundException('brokers.message.error.missing');
            }
        });

        Route::bind('client', function ($value) {
            try {
                return \App\Models\User::withStatus()->findOrFail($value);
            } catch (ModelNotFoundException $e) {
                throw new InsuraModelNotFoundException('clients.message.error.missing');
            }
        });

        Route::bind('recipient', function ($value) {
            try {
                return \App\Models\User::withStatus()->findOrFail($value);
            } catch (ModelNotFoundException $e) {
                throw new InsuraModelNotFoundException('users.message.error.missing');
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
