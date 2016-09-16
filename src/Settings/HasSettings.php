<?php

namespace LaravelPropertyBag\Settings;

use LaravelPropertyBag\Settings\Settings;
use LaravelPropertyBag\Helpers\NameResolver;
use LaravelPropertyBag\Settings\PropertyBag;
use LaravelPropertyBag\UserSettings\UserSettings;
use LaravelPropertyBag\Exceptions\ResourceNotFound;
use LaravelPropertyBag\UserSettings\UserPropertyBag;

trait HasSettings
{
    /**
     * Instance of Settings.
     *
     * @var LaravelPropertyBag\Settings\Settings
     */
    protected $settings = null;

    /**
     * A resource has many settings in a property bag.
     *
     * @return HasMany
     */
    public function propertyBag()
    {
        return $this->morphMany(PropertyBag::class, 'resource');
    }

    /**
     * Get resource id.
     *
     * @return int
     */
    public function resourceId()
    {
        return $this->id;
    }

    /**
     * Get settings class for the resource.
     *
     * @param Collection $allowed
     *
     * @return Settings
     */
    public function settings($allowed = null)
    {
        if (isset($this->settings)) {
            return $settings;
        }

        $settingsConfig = $this->getSettingsConfig();

        return $this->settings = new Settings($settingsConfig, $this);
    }

    /**
     * Get the settings class name.
     *
     * @return string
     */
    protected function getSettingsConfig()
    {
        if (isset($this->settingsConfig)) {
            $fullNamespace = $this->settingsConfig;
        } else {
            $fullNamespace = $this->getFullNamespace();
        }

        if (class_exists($fullNamespace)) {
            return new $fullNamespace;
        }

        throw ResourceNotFound::settingsConfigNotFound($fullNamespace);
    }

    /**
     * Get the full namespace of the settings config class.
     *
     * @return string
     */
    protected function getFullNamespace()
    {
        $appNamespace = NameResolver::getAppNamespace();

        $className = $this->getShortClassName();

        return $appNamespace.'Settings\\'.$className.'Settings';
    }

    /**
     * Get the short name of the model.
     *
     * @return string
     */
    protected function getShortClassName()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection;
    }

    // /**
    //  * Get all set settings for resource.
    //  *
    //  * @return Collection
    //  */
    // public function allSettings()
    // {
    //     return $this->propertyBag;
    // }

    // /**
    //  * Get all settings as a flat collection.
    //  *
    //  * @return Collection
    //  */
    // public function allSettingsFlat()
    // {
    //     return $this->allSettings()->flatMap(function ($model) {
    //         return [$model->key => json_decode($model->value)[0]];
    //     });
    // }

    // /**
    //  * Get the property bag class name.
    //  *
    //  * @return string
    //  */
    // public function getPropertyBagClass()
    // {
    //     $classNamespace = $this->makeNamespace([$this, 'getPropertyBagName']);

    //     if (isset($this->propertyBagClass)) {
    //         return $this->propertyBagClass;
    //     } elseif (class_exists($classNamespace)) {
    //         return $classNamespace;
    //     }

    //     return UserPropertyBag::class;
    // }

    // /**
    //  * Get name for settings.
    //  *
    //  * @return string
    //  */
    // protected function getSettingsName()
    // {
    //     return $this->getShortClassName().'Settings';
    // }

    // /**
    //  * Get name for property bag.
    //  *
    //  * @return string
    //  */
    // protected function getPropertyBagName()
    // {
    //     return $this->getShortClassName().'PropertyBag';
    // }

    // /**
    //  * Make class namespace.
    //  *
    //  * @param callable $callback
    //  *
    //  * @return string
    //  */
    // protected function makeNamespace(callable $callback)
    // {
    //     $settingsName = $this->getSettingsName();

    //     $appNamespace = NameResolver::getAppNamespace();

    //     return $appNamespace.$settingsName.'\\'.$callback();
    // }
}
