<?php

namespace LaravelPropertyBag\Settings;

use LaravelPropertyBag\Helpers\NameResolver;
use LaravelPropertyBag\Exceptions\ResourceNotFound;

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
     * @return MorphMany
     */
    public function propertyBag()
    {
        return $this->morphMany(PropertyBag::class, 'resource');
    }

    /**
     * Get settings class for the resource.
     *
     * @return Settings
     */
    public function settings()
    {
        if (isset($this->settings)) {
            return $this->settings;
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
            return new $fullNamespace();
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

        return $reflection->getShortName();
    }

    // Accessors
    // settings($key) -> if $key, return keys settings
    // setSetting($key, $value)
    // allSettings()
    // defaultSetting($key) -> if $key, return that keys setting, else return all
    // allowedSettings($key) -> same as above
    // 
}
