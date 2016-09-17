<?php

namespace LaravelPropertyBag\tests\Classes;

use LaravelPropertyBag\Settings\Settings;
use LaravelPropertyBag\Settings\ResourceConfig;

class UserConfig extends ResourceConfig
{
    /**
     * Namespace of resource.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * Registered settings for the user. Register settings by setting name. Each
     * setting must have an associative array set as its value that contains an
     * array of 'allowed' values and a single 'default' value.
     *
     * @var array
     */
    protected $registeredSettings = [
        'test_settings1' => [
            'allowed' => ['bananas', 'grapes', 8, 'monkey'],
            'default' => 'monkey'
        ],

        'test_settings2' => [
            'allowed' => [true, false],
            'default' => true
        ],
        
        'test_settings3' => [
            'allowed' => [true, false, 'true', 'false', 0, 1, '0', '1'],
            'default' => false
        ]
    ];
}
