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
    public function __construct(Model $resource, $registered = null)
    {
        $this->resource = $resource;

        $this->sync();

        $this->registered = $this->setRegistered($registered);
    }

    /**
     * Get the registered and default values from config or given array.
     *
     * @param array|null $registered
     *
     * @return Collection
     */
    protected function setRegistered($registered)
    {
        if (is_null($registered)) {
            return collect($this->registeredSettings);
        }

        return collect($registered);
    }

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

        return null;
    }

    /**
     * Get all defaults for settings.
     *
     * @return Collection
     */
    public function allDefaults()
    {
        return $this->registered->map(function ($value, $key) {
            return $value['default'];
        });
    }

    /**
     * Get the allowed settings for key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAllowed($key)
    {
        if ($this->isRegistered($key)) {
            return $this->registered[$key]['allowed'];
        }

        return null;
    }

    /**
     * Get all allowed values for settings.
     *
     * @return Collection
     */
    public function allAllowed()
    {
        return $this->registered->map(function ($value, $key) {
            return $value['allowed'];
        });
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
     * Set a value to a key in local and database settings.
     *
     * @param string $key
     * @param mixed  $value
     */
    protected function setKeyValue($key, $value)
    {
        if ($this->isValid($key, $value)) {
            $method = $this->getSyncType($key, $value);

            if ($method !== 'pass') {
                $this->{$method}($key, $value);
            }
        }
    }

    /**
     * Get the type of database operation to perform for the sync.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return string
     */
    protected function getSyncType($key, $value)
    {
        if ($this->isDefault($key, $value) && !$this->hasSetting($key)) {
            return 'pass';
        } elseif ($this->isDefault($key, $value)) {
            return 'deleteRecord';
        } elseif ($this->hasSetting($key)) {
            return 'updateRecord';
        }

        return 'newRecord';
    }

    /**
     * Return all resource settings as array.
     *
     * @return array
     */
    public function all()
    {
        return $this->settings->all();
    }

    /**
     * Return all settings used by resource, including defaults.
     *
     * @return Collection
     */
    public function allSettings()
    {
        $set = collect($this->all());

        return $this->allDefaults()->map(function ($value, $key) use ($set) {
            if ($set->has($key)) {
                return $set->get($key);
            }

            return $value;
        });
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
     * Return true if value is default value for key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function isDefault($key, $value)
    {
        return $this->getDefault($key) === $value;
    }

    /**
     * Return true if key and value are registered values.
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
     * Get settings from the resource relationship.
     */
    public function sync()
    {
        $this->resource->load('propertyBag');

        $this->settings = $this->resource->allSettingsFlat();
    }

    /**
     * Create a new UserSettings record.
     *
     * @param string $key
     *
     * @return UserSettings
     */
    protected function newRecord($key, $value)
    {
        $model = $this->resource->getPropertyBagClass();

        return $model::create([
            $this->primaryKey => $this->resource->resourceId(),
            'key' => $key,
            'value' => json_encode([$value]),
        ]);
    }

    /**
     * Update a UserSettings record.
     *
     * @param string $key
     *
     * @return UserSettings
     */
    protected function updateRecord($key, $value)
    {
        $model = $this->resource->getPropertyBagClass();

        $record = $model::where(
            $this->primaryKey,
            '=',
            $this->resource->resourceId()
        )->where('key', '=', $key)->first();

        if (!is_null($record)) {
            $record->value = json_encode([$value]);

            $record->save();

            return $record;
        }
    }

    /**
     * Delete a UserSettings record.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    protected function deleteRecord($key, $value)
    {
        $model = $this->resource->getPropertyBagClass();

        $record = $model::where(
            $this->primaryKey,
            '=',
            $this->resource->resourceId()
        )->where('key', '=', $key)->first();

        if (!is_null($record)) {
            return $record->delete();
        }
    }
}
