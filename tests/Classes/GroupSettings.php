<?php

namespace LaravelPropertyBag\tests\Classes;

use LaravelPropertyBag\Settings\Settings;

class GroupSettings extends Settings
{
    /**
     * Primary key for the resource settings table.
     *
     * @var string
     */
    protected $primaryKey = 'group_id';

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
            return config('laravel-property-bag.allowed_group_settings');
        }

        return $registered;
    }
}
