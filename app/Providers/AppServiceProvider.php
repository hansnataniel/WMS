<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        /*
            cOMMAND THIS SCRIPT IF U DON'T USE MULTI AUTH
        */
        $this->app['router']->matched(function (\Illuminate\Routing\Events\RouteMatched $event) {
            $route = $event->route;
            if (!array_has($route->getAction(), 'guard')) {
                return;
            }
            $routeGuard = array_get($route->getAction(), 'guard');
            $this->app['auth']->resolveUsersUsing(function ($guard = null) use ($routeGuard) {
                return $this->app['auth']->guard($routeGuard)->user();
            });
            $this->app['auth']->setDefaultDriver($routeGuard);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
