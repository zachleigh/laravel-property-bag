<?php

namespace LaravelPropertyBag;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use LaravelPropertyBag\Settings\PropertyBag;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PropertyBagServiceProvider extends ServiceProvider
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
            __DIR__.'/../config/property-bag.php' => config_path('property-bag.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/property-bag.php', 'property-bag');

        $this->publishes([
            __DIR__.'/Migrations/' => database_path('migrations'),
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

    public static function determinePropertyBagModel(): string
    {
        $propertyBagModel = config('property-bag.property_bag_model') ?? PropertyBag::class;

        if (!is_a($propertyBagModel, Model::class, true)) {
            throw new ModelNotFoundException($propertyBagModel);
        }

        return $propertyBagModel;
    }

    public static function getPropertyBagModelInstance(): Model
    {
        $propertyBagModelClassName = self::determinePropertyBagModel();

        return new $propertyBagModelClassName();
    }
}
