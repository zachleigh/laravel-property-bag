<?php

namespace {{Namespace}};

use LaravelPropertyBag\Settings\ResourceConfig;

class {{ClassName}} extends ResourceConfig
{
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
