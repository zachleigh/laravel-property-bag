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
     * Get settings class for the resource or return value for given key.
     *
     * @param string $key
     *
     * @return Settings|mixed
     */
    public function settings($key = null)
    {
        if (!is_null($key)) {
            $settings = $this->getSettingsInstance();

            return $settings->get($key);
        }

        return $this->getSettingsInstance();
    }

    /**
     * Get settings off this or create new instance.
     *
     * @return Settings
     */
    protected function getSettingsInstance()
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
            $className = $this->getShortClassName();

            $fullNamespace = NameResolver::makeConfigFileName($className);
        }

        if (class_exists($fullNamespace)) {
            return new $fullNamespace();
        }

        throw ResourceNotFound::resourceConfigNotFound($fullNamespace);
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

    /**
     * Set settings.
     *
     * @param array $attributes
     *
     * @return Settings
     */
    public function setSettings(array $attributes)
    {
        return $this->settings()->set($attributes);
    }

    /**
     * Get all settings.
     *
     * @return Collection
     */
    public function allSettings()
    {
        return $this->settings()->all();
    }

    /**
     * Get all default settings or default setting for single key if given.
     *
     * @param string $key
     *
     * @return Collection|mixed
     */
    public function defaultSetting($key = null)
    {
        if (!is_null($key)) {
            return $this->settings()->getDefault($key);
        }

        return $this->settings()->allDefaults();
    }

    /**
     * Get all allowed settings or allowed settings for single ke if given.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function allowedSetting($key = null)
    {
        if (!is_null($key)) {
            return $this->settings()->getAllowed($key);
        }

        return $this->settings()->allAllowed();
    }
}
