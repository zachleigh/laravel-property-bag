<?php

namespace LaravelPropertyBag;

use Auth;
use LaravelPropertyBag\Helpers\NameResolver;
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
        $this->registerCommands();
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register Artisan commands.
     */
    protected function registerCommands()
    {
        $this->app->singleton('command.pbag.make', function ($app) {
            return $app['LaravelPropertyBag\Commands\PublishSettingsConfig'];
        });

        $this->app->singleton('command.pbag.rules', function ($app) {
            return $app['LaravelPropertyBag\Commands\PublishRulesFile'];
        });

        $this->commands('command.pbag.make');

        $this->commands('command.pbag.rules');
    }
}
