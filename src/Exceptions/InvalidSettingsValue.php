<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class InvalidSettingsValue extends Exception
{
    public static function settingNotAllowed($key, $value)
    {
        return new static("{$value} is not a registered allowed value for {$key}.");
    }
}
