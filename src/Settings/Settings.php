<?php

namespace LaravelPropertyBag\Settings;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\Settings\Rules\RuleValidator;
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
     * Settings saved in database. Does not include defaults.
     *
     * @var Collection
     */
    protected $settings;

    /**
     * Validator for allowed rules.
     *
     * @var LaravelPropertyBag\Settings\Rules\RuleValidator
     */
    protected $ruleValidator;

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

        $this->ruleValidator = new RuleValidator();
        $this->registered = $settingsConfig->registeredSettings();

        $this->sync();
    }

    /**
     * Get the property bag relationshp off the resource.
     *
     * @return MorphMany
     */
    protected function propertyBag()
    {
        return $this->resource->propertyBag();
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
        return $this->getRegistered()->has($key);
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
        $settings = collect(
            $this->getRegistered()->get($key, ['allowed' => []])
        );

        $allowed = $settings->get('allowed');

        if (!is_array($allowed) &&
            $rule = $this->ruleValidator->isRule($allowed)) {
            return $this->ruleValidator->validate($rule, $value);
        }

        return in_array($value, $allowed, true);
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
            return $this->getRegistered()[$key]['default'];
        }
    }

    /**
     * Return all settings used by resource, including defaults.
     *
     * @return Collection
     */
    public function all()
    {
        $saved = $this->allSaved();

        return $this->allDefaults()->map(function ($value, $key) use ($saved) {
            if ($saved->has($key)) {
                return $saved->get($key);
            }

            return $value;
        });
    }

    /**
     * Get all defaults for settings.
     *
     * @return Collection
     */
    public function allDefaults()
    {
        return $this->getRegistered()->map(function ($value, $key) {
            return $value['default'];
        });
    }

    /**
     * Get the allowed settings for key.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function getAllowed($key)
    {
        if ($this->isRegistered($key)) {
            return collect($this->getRegistered()[$key]['allowed']);
        }
    }

    /**
     * Get all allowed values for settings.
     *
     * @return Collection
     */
    public function allAllowed()
    {
        return $this->getRegistered()->map(function ($value, $key) {
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
     * Return true if key is set to value.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function isSet($key, $value)
    {
        return $this->get($key) === $value;
    }

    /**
     * Reset key to default value. Return default value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function reset($key)
    {
        $default = $this->getDefault($key);

        $this->set([$key => $default]);

        return $default;
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
            if ($this->isDefault($key, $value) && $this->isSaved($key)) {
                return $this->deleteRecord($key, $value);
            } elseif ($this->isDefault($key, $value)) {
                return;
            } elseif ($this->isSaved($key)) {
                return $this->updateRecord($key, $value);
            }

            return $this->createRecord($key, $value);
        }

        throw InvalidSettingsValue::settingNotAllowed($key);
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
     * Create a new PropertyBag record.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return PropertyBag
     */
    protected function createRecord($key, $value)
    {
        return $this->propertyBag()->save(
            new PropertyBag([
                'key'   => $key,
                'value' => $this->valueToJson($value),
            ])
        );
    }

    /**
     * Update a PropertyBag record.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return PropertyBag
     */
    protected function updateRecord($key, $value)
    {
        $record = $this->getByKey($key);

        $record->value = $this->valueToJson($value);

        $record->save();

        return $record;
    }

    /**
     * Json encode value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function valueToJson($value)
    {
        return json_encode([$value]);
    }

    /**
     * Delete a PropertyBag record.
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
     * @param string $key
     *
     * @return PropertyBag
     */
    protected function getByKey($key)
    {
        return $this->propertyBag()
            ->where('resource_id', $this->resource->id)
            ->where('key', $key)
            ->first();
    }

    /**
     * Load settings from the resource relationship on to this.
     */
    protected function sync()
    {
        $this->resource->load('propertyBag');

        $this->settings = $this->getAllSettingsFlat();
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
        return $this->propertyBag()
            ->where('resource_id', $this->resource->id)
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
}
