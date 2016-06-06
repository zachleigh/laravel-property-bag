<?php

namespace LaravelPropertyBag\UserSettings;

use LaravelPropertyBag\Settings\Settings;

class UserSettings extends Settings
{
    /**
     * Primary key for the resource property bag table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Registered settings for the user. Register settings by setting name. Each
     * setting must have an associative array set as its value that contains an
     * array of 'allowed' values and a single 'default' value.
     *
     * @var array
     */
    protected $registeredSettings = [

        // 'example_setting' => [
        //     'allowed' => [true, false],
        //     'default' => true
        // ]

    ];
}
