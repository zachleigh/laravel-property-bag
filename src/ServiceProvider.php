<?php

namespace LaravelPropertyBag;

use Auth;
use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('LaravelPropertyBag\Settings\UserSettings', function () {
            return Auth::user()->settings();
        });

        $this->app->singleton('command.lpb.user', function ($app) {
            return $app['LaravelPropertyBag\Commands\PublishUserSettings'];
        });

        $this->commands('command.lpb.user');
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('laravel-property-bag.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/Migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
