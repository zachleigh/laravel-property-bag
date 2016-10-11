<?php

namespace LaravelPropertyBag\tests\Classes;

use LaravelPropertyBag\Settings\ResourceConfig;

class CommentConfig extends ResourceConfig
{
    /**
     * Registered settings for the user. Register settings by setting name. Each
     * setting must have an associative array set as its value that contains an
     * array of 'allowed' values and a single 'default' value.
     *
     * @var array
     */
    protected $registeredSettings = [
        'invalid' => [
            'allowed' => ':nope:',
            'default' => null,
        ],
        'any' => [
            'allowed' => ':any:',
            'default' => 'something',
        ],
        'alpha' => [
            'allowed' => ':alpha:',
            'default' => 'something',
        ],
        'alphanum' => [
            'allowed' => ':alphanum:',
            'default' => 'something',
        ],
        'bool' => [
            'allowed' => ':bool:',
            'default' => false,
        ],
        'integer' => [
            'allowed' => ':int:',
            'default' => 7,
        ],
        'numeric' => [
            'allowed' => ':num:',
            'default' => 5,
        ],
        'range' => [
            'allowed' => ':range=1,5:',
            'default' => 1,
        ],
        'range2' => [
            'allowed' => ':range=-10,5:',
            'default' => 0,
        ],
        'string' => [
            'allowed' => ':string:',
            'default' => 'test',
        ],
        'user_defined' => [
            'allowed' => ':example:',
            'default' => true,
        ],
    ];
}
