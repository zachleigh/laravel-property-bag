<?php

namespace LaravelPropertyBag\Settings;

use Illuminate\Database\Eloquent\Model;

abstract class Settings
{
    /**
     * Resource that has settings.
     *
     * @var Model
     */
    protected $resource;

    /**
     * Settings for resource.
     *
     * @var Collection
     */
    protected $settings;

    /**
     * Settings values that have been changed since last sync.
     *
     * @var array
     */
    protected $changed = [];

    /**
     * Registered keys, values, and defaults.
     * 'key' => ['allowed' => $value, 'default' => $value].
     *
     * @var Collection
     */
    protected $registered;

    /**
     * Null array for isValid method.
     *
     * @var array
     */
    protected $nullRegistered = [
        'allowed' => [],
    ];

    /**
     * Construct.
     *
     * @param Model      $resource
     * @param Collection $registered
     */
    public function __construct($resource, $registered = null)
    {
        $this->resource = $resource;

        $this->refreshSettings();

        $this->registered = $this->getRegistered($registered);
    }

    /**
     * Get the registered and default values from config or given Collection.
     *
     * @param Collection|null $config
     *
     * @return Collection
     */
    abstract protected function getRegistered($registered);

    /**
     * Get value from settings by key. Get registered default if not set.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->settings->get($key, function () use ($key) {
            return $this->getDefault($key);
        });
    }

    /**
     * Get the default value from registered.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getDefault($key)
    {
        if ($this->isRegistered($key)) {
            return $this->registered[$key]['default'];
        }

        return;
    }

    /**
     * Update or add multiple values to the settings table.
     *
     * @param array $attributes
     *
     * @return this
     */
    public function set(array $attributes)
    {
        collect($attributes)->each(function ($value, $key) {
            $this->setKeyValue($key, $value, false);
        });

        return $this->sync();
    }

    /**
     * Set a value in local and db settings.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return this
     */
    protected function setKeyValue($key, $value)
    {
        if ($this->isValid($key, $value)) {
            $syncType = ($this->hasSetting($key) ? 'update' : 'new');

            $this->settings[$key] = $value;

            return $this->flagAsChanged($key, $syncType);
        }
    }

    /**
     * Return all resource settings.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->settings;
    }

    /**
     * Return true if key is set in settings.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasSetting($key)
    {
        return $this->settings->has($key);
    }

    /**
     * Return true if key exists in registered settings collection.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isRegistered($key)
    {
        return $this->registered->has($key);
    }

    /**
     * Key and value are registered values.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function isValid($key, $value)
    {
        $settingArray = collect(
            $this->registered->get($key, $this->nullRegistered)
        );

        return in_array($value, $settingArray->get('allowed'), true);
    }

    /**
     * Add key to changed array.
     *
     * @param string $key
     * @param string $syncType ['new', 'update']
     *
     * @return this
     */
    public function flagAsChanged($key, $syncType)
    {
        $this->changed[$key] = $syncType;

        return $this;
    }

    /**
     * Get settings from the resource relationship.
     */
    public function refreshSettings()
    {
        $this->settings = $this->resource->allSettings();
    }

    /**
     * Sync local settings collection with database.
     *
     * @return this
     */
    public function sync()
    {
        collect($this->changed)->each(function ($syncType, $key) {
            $method = $syncType.'Record';

            $this->{$method}($key);
        });

        return $this->clearChanged();
    }

    /**
     * Create a new UserSettings record.
     *
     * @param string $key
     *
     * @return UserSettings
     */
    protected function newRecord($key)
    {
        $model = $this->resource->getPropertyBagClass();

        return $model::create([
            $this->primaryKey => $this->resource->id(),
            'key' => $key,
            'value' => $this->get($key),
        ]);
    }

    /**
     * Update a UserSettings record.
     *
     * @param string $key
     *
     * @return UserSettings
     */
    protected function updateRecord($key)
    {
        $model = $this->resource->getPropertyBagClass();

        return $model::where($this->primaryKey, '=', $this->resource->id())
            ->where('key', '=', $key)
            ->update(['value' => $this->get($key)]);
    }

    /**
     * Clear changed records.
     *
     * @return this
     */
    protected function clearChanged()
    {
        $this->changed = [];

        return $this;
    }
}
