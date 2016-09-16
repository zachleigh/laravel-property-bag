<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class InvalidSettingsValue extends Exception
{
    /**
     * Setting value is not definied in allowed values array.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return static
     */
    public static function settingNotAllowed($key, $value)
    {
        return new static("{$value} is not a registered allowed value for {$key}.");
    }
}
