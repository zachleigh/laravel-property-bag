<?php

namespace LaravelPropertyBag\User;

use LaravelPropertyBag\Settings\Settings;

class UserSettings extends Settings
{
    /**
     * Primary key for the resource settings table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Get the registered and default values from config or given Collection.
     *
     * @param array|null $config
     *
     * @return  Collection
     */
    protected function getRegistered($registered)
    {
        if (is_null($registered)) {
            return config('laravel-property-bag.registered_user_settings');
        }

        return $registered;
    }
}
