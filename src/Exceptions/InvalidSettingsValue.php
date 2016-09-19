<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class InvalidSettingsValue extends Exception
{
    /**
     * Setting value is not definied in key's allowed values array.
     *
     * @param  string $key
     *
     * @return static
     */
    public static function settingNotAllowed($key)
    {
        return new static("Given value is not a registered allowed value for {$key}.");
    }
}
