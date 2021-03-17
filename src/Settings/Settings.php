<?php

namespace LaravelPropertyBag\Settings;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\PropertyBagServiceProvider;
use LaravelPropertyBag\Settings\Rules\RuleValidator;
use LaravelPropertyBag\Exceptions\InvalidSettingsValue;

class Settings
{
    /**
     * Settings for resource.
     *
     * @var \LaravelPropertyBag\Settings\ResourceConfig
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
     * @var \Illuminate\Support\Collection
     */
    protected $registered;

    /**
     * Settings saved in database. Does not include defaults.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $settings;

    /**
     * Validator for allowed rules.
     *
     * @var \LaravelPropertyBag\Settings\Rules\RuleValidator
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    protected function propertyBag()
    {
        return $this->resource->propertyBag();
    }

    /**
     * Get resource config.
     *
     * @return \LaravelPropertyBag\Settings\ResourceConfig
     */
    public function getResourceConfig()
    {
        return $this->settingsConfig;
    }

    /**
     * Get registered settings.
     *
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
     */
    public function allDefaults()
    {
        return $this->getRegistered()->map(function ($value) {
            return $value['default'];
        });
    }

    /**
     * Get the allowed settings for key.
     *
     * @param string $key
     *
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
     */
    public function allAllowed()
    {
        return $this->getRegistered()->map(function ($value) {
            return $value['allowed'];
        });
    }

    /**
     * Get all saved settings. Default values are not included in this output.
     *
     * @return \Illuminate\Support\Collection
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
     * @return static
     */
    public function set(array $attributes)
    {
        collect($attributes)->each(function ($value, $key) {
            $this->setKeyValue($key, $value);
        });

        // If we were working with eagerly-loaded relation,
        // we need to reload its data to be sure that we
        // are working only with the actual settings.

        if ($this->resource->relationLoaded('propertyBag')) {
            $this->resource->load('propertyBag');
        }

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
    public function keyIs($key, $value)
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
     * @return mixed
     */
    protected function setKeyValue($key, $value)
    {
        $this->validateKeyValue($key, $value);

        if ($this->isDefault($key, $value) && $this->isSaved($key)) {
            return $this->deleteRecord($key);
        } elseif ($this->isDefault($key, $value)) {
            return;
        } elseif ($this->isSaved($key)) {
            return $this->updateRecord($key, $value);
        }

        return $this->createRecord($key, $value);
    }

    /**
     * Throw exception if key/value invalid.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidSettingsValue
     */
    protected function validateKeyValue($key, $value)
    {
        if (!$this->isValid($key, $value)) {
            throw InvalidSettingsValue::settingNotAllowed($key);
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
     * Create a new PropertyBag record.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \LaravelPropertyBag\Settings\PropertyBag
     */
    protected function createRecord($key, $value)
    {
        $propertyBagModel = PropertyBagServiceProvider::determinePropertyBagModel();
        return $this->propertyBag()->save(
            new $propertyBagModel([
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
     * @return \LaravelPropertyBag\Settings\PropertyBag
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
     *
     * @return bool
     */
    protected function deleteRecord($key)
    {
        $this->getByKey($key)->delete();
    }

    /**
     * Get a property bag record by key.
     *
     * @param string $key
     *
     * @return \LaravelPropertyBag\Settings\PropertyBag
     */
    protected function getByKey($key)
    {
        return $this->propertyBag()
            ->where('resource_id', $this->resource->getKey())
            ->where('key', $key)
            ->first();
    }

    /**
     * Load settings from the resource relationship on to this.
     */
    protected function sync()
    {
        $this->settings = $this->getAllSettingsFlat();
    }

    /**
     * Get all settings as a flat collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAllSettingsFlat()
    {
        return $this->getAllSettings()->flatMap(function (Model $model) {
            return [$model->key => json_decode($model->value)[0]];
        });
    }

    /**
     * Retrieve all settings from database.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAllSettings()
    {
        if ($this->resource->relationLoaded('propertyBag')) {
            return $this->resource->propertyBag;
        }

        return $this->propertyBag()
            ->where('resource_id', $this->resource->getKey())
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
