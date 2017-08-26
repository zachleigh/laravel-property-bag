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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function propertyBag()
    {
        return $this->morphMany(PropertyBag::class, 'resource');
    }

    /**
     * If passed is string, get settings class for the resource or return value
     * for given key. If passed is array, set the key value pair.
     *
     * @param string|array $passed
     *
     * @return LaravelPropertyBag\Settings\Settings|mixed
     */
    public function settings($passed = null)
    {
        if (is_array($passed)) {
            return $this->setSettings($passed);
        } elseif (!is_null($passed)) {
            $settings = $this->getSettingsInstance();

            return $settings->get($passed);
        }

        return $this->getSettingsInstance();
    }

    /**
     * Get settings off this or create new instance.
     *
     * @return LaravelPropertyBag\Settings\Settings
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
     * @throws ResourceNotFound
     *
     * @return LaravelPropertyBag\Settings\ResourceConfig
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
            return new $fullNamespace($this);
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
     * @return LaravelPropertyBag\Settings\Settings
     */
    public function setSettings(array $attributes)
    {
        return $this->settings()->set($attributes);
    }

    /**
     * Set all allowed settings by Request.
     *
     * @return LaravelPropertyBag\Settings\Settings
     */
    public function setSettingsByRequest()
    {
        $allAllowedSettings = array_keys($this->allSettings()->toArray());

        return $this->settings()->set(request()->only($allAllowedSettings));
    }

    /**
     * Get all settings.
     *
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection|mixed
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
     * @return \Illuminate\Support\Collection
     */
    public function allowedSetting($key = null)
    {
        if (!is_null($key)) {
            return $this->settings()->getAllowed($key);
        }

        return $this->settings()->allAllowed();
    }
}
