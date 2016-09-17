<?php

namespace LaravelPropertyBag\Settings;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\Exceptions\InvalidSettingsValue;

class Settings
{
    /**
     * Settings for resource.
     *
     * @var ResourceConfig
     */
    protected $settingsConfig;

    /**
     * Resource that has settings.
     *
     * @var Model
     */
    protected $resource;

    /**
     * Registered keys, values, and defaults.
     * 'key' => ['allowed' => $value, 'default' => $value].
     *
     * @var Collection
     */
    protected $registered;

    /**
     * Saved settings.
     *
     * @var array
     */
    protected $settings = [];

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
     * @param ResourceConfig $settingsConfig
     * @param Model          $resource
     */
    public function __construct(ResourceConfig $settingsConfig, Model $resource)
    {
        $this->settingsConfig = $settingsConfig;
        $this->resource = $resource;

        $this->registered = $settingsConfig->getRegisteredSettings();

        $this->sync();
    }

    /**
     * Get registered settings.
     *
     * @return Collection
     */
    public function getRegistered()
    {
        return $this->registered;
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

        return;
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
     * Get all saved settings. Default values are not included in this output.
     *
     * @return Collection
     */
    public function allSaved()
    {
        return collect($this->settings);
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
     *
     * @return PropertyBag|InvalidSettingsValue
     */
    protected function setKeyValue($key, $value)
    {
        if ($this->isValid($key, $value)) {
            if ($this->isSaved($key)) {
                return $this->updateRecord($key, $value);
            } else if ($this->isDefault($key, $value)) {
                return $this->deleteRecord($key, $value);
            }

            return $this->createRecord($key, $value);
        } else {
            throw InvalidSettingsValue::settingNotAllowed($key, $value);
        }
    }

    /**
     * Return true if key is already saved in database.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isSaved($key)
    {
        return $this->allSaved()->has($key);
    }

    /**
     * Create a new UserSettings record.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return UserSettings
     */
    protected function createRecord($key, $value)
    {
        return $this->resource->propertyBag()->save(
            new PropertyBag([
                'user_id' => auth()->id(),
                'key' => $key,
                'value' => json_encode([$value]),
            ])
        );
    }

    /**
     * Update a UserSettings record.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return UserSettings
     */
    protected function updateRecord($key, $value)
    {
        $record = $this->getByKey($key);

        $record->value = json_encode([$value]);

        $record->save();

        return $record;
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
        $record = $this->getByKey($key)->delete();
    }

    /**
     * Get a property bag record by key.
     *
     * @param  string $key
     *
     * @return PropertyBag
     */
    protected function getByKey($key)
    {
        return $this->resource->propertyBag()
            ->where('user_id', auth()->id())
            ->where('key', $key)
            ->first();
    }

    /**
     * Get settings from the resource relationship.
     */
    protected function sync()
    {
        $this->resource->load('propertyBag');

        $this->settings = $this->getAllSettingsFlat()->all();
    }

    /**
     * Get all settings as a flat collection.
     *
     * @return Collection
     */
    protected function getAllSettingsFlat()
    {
        return $this->getAllSettings()->flatMap(function ($model) {
            return [$model->key => json_decode($model->value)[0]];
        });
    }

    /**
     * Retrieve all settings from database.
     *
     * @return Collection
     */
    protected function getAllSettings()
    {
        return $this->resource->propertyBag()
            ->where('user_id', auth()->id())
            ->get();
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
        return $this->allSaved()->get($key, function () use ($key) {
            return $this->getDefault($key);
        });
    }












    // /**
    //  * Get the type of database operation to perform for the sync.
    //  *
    //  * @param string $key
    //  * @param mixed  $value
    //  *
    //  * @return string
    //  */
    // protected function getSyncType($key, $value)
    // {
    //     if ($this->isDefault($key, $value) && !$this->hasSetting($key)) {
    //         return 'pass';
    //     } elseif ($this->isDefault($key, $value)) {
    //         return 'deleteRecord';
    //     } elseif ($this->hasSetting($key)) {
    //         return 'updateRecord';
    //     }

    //     return 'newRecord';
    // }

    // /**
    //  * Return all resource settings as array.
    //  *
    //  * @return array
    //  */
    // public function all()
    // {
    //     return $this->settings->all();
    // }

    // /**
    //  * Return all settings used by resource, including defaults.
    //  *
    //  * @return Collection
    //  */
    // public function allSettings()
    // {
    //     $set = collect($this->all());

    //     return $this->allDefaults()->map(function ($value, $key) use ($set) {
    //         if ($set->has($key)) {
    //             return $set->get($key);
    //         }

    //         return $value;
    //     });
    // }

}
