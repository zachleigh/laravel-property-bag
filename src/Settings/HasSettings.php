<?php

namespace LaravelPropertyBag\Settings;

use LaravelPropertyBag\UserSettings\UserSettings;
use Illuminate\Console\AppNamespaceDetectorTrait;
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
        if ($this->settingsInstance) {
            return $this->settingsInstance;
        }

        $settingsClass = $this->getSettingsClass();

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
     * Get all settings as a flat collection.
     *
     * @return Collection
     */
    public function allSettingsFlat()
    {
        return $this->allSettings()->flatMap(function ($model) {
            return [$model->key => json_decode($model->value)[0]];
        });
    }

    /**
     * Get the settings class name.
     *
     * @return string
     */
    protected function getSettingsClass()
    {
        $classNamespace = $this->makeNamespace([$this, 'getSettingsName']);

        if (isset($this->settingsClass)) {
            return $this->settingsClass;
        } elseif (class_exists($classNamespace)) {
            return $classNamespace;
        }

        return UserSettings::class;
    }

    /**
     * Get the property bag class name.
     *
     * @return string
     */
    public function getPropertyBagClass()
    {
        $classNamespace = $this->makeNamespace([$this, 'getPropertyBagName']);

        if (isset($this->propertyBagClass)) {
            return $this->propertyBagClass;
        } elseif (class_exists($classNamespace)) {
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
     * Make class namespace.
     *
     * @param callable $callback
     *
     * @return string
     */
    protected function makeNamespace(callable $callback)
    {
        $settingsName = $this->getSettingsName();

        $appNamespace = $this->getAppNamespace();

        return $appNamespace.$settingsName.'\\'.$callback();
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
