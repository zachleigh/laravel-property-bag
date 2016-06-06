<?php

namespace LaravelPropertyBag\Settings;

use LaravelPropertyBag\UserSettings\UserSettings;
use \Illuminate\Console\AppNamespaceDetectorTrait;
use LaravelPropertyBag\UserSettings\UserPropertyBag;

trait HasSettings
{
    use AppNamespaceDetectorTrait;

    /**
     * Saved settings.
     *
     * @var Collection
     */
    protected $settingsInstance = null;

    /**
     * Get user id.
     *
     * @return int
     */
    public function id()
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
        $settingsClass = $this->getSettingsClass();

        if ($this->settingsInstance) {
            return $this->settingsInstance;
        }

        return $this->settingsInstance = new $settingsClass($this, $allowed);
    }

    /**
     * A resource has many settings in a property bag.
     *
     * @return HasMany
     */
    public function propertyBag()
    {
        $propertyBagClass = $this->getPropertyBagClass();

        return $this->hasMany($propertyBagClass);
    }

    /**
     * Get all set settings for resource.
     *
     * @return Collection
     */
    public function allSettings()
    {
        return $this->propertyBag;
    }

    /**
     * Get the settings class name.
     *
     * @return string
     */
    protected function getSettingsClass()
    {
        $settingsName = $this->getSettingsName();

        $appNamespace = $this->getAppNamespace();

        $classNamespace = $appNamespace.$settingsName.'\\'.$settingsName;

        if (isset($this->settingsClass)) {
            return $this->settingsClass;
        } else if (class_exists($classNamespace)) {
            return $classNamespace;
        }
        
        return UserSettings::class;
    }

    /**
     * Get the settings class name.
     *
     * @return string
     */
    public function getPropertyBagClass()
    {
        $settingsName = $this->getSettingsName();

        $propertyBagName = $this->getPropertyBagName();

        $appNamespace = $this->getAppNamespace();

        $classNamespace = $appNamespace.$settingsName.'\\'.$propertyBagName;

        if (isset($this->propertyBagClass)) {
            return $this->propertyBagClass;
        } else if (class_exists($classNamespace)) {
            return $classNamespace;
        }

        return UserPropertyBag::class;
    }

    /**
     * Get name for settings.
     *
     * @return string
     */
    protected function getSettingsName()
    {
        return $this->getShortClassName().'Settings';
    }

    /**
     * Get name for property bag.
     *
     * @return string
     */
    protected function getPropertyBagName()
    {
        return $this->getShortClassName().'PropertyBag';
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
}
