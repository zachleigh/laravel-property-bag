<?php

namespace LaravelPropertyBag\Settings;

use LaravelPropertyBag\UserPropertyBag;

trait HasSettings
{
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

        return new $settingsClass($this, $allowed);
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
        if (isset($this->settingsClass)) {
            return $this->settingsClass;
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
        if (isset($this->propertyBagClass)) {
            return $this->propertyBagClass;
        }

        return UserPropertyBag::class;
    }
}
