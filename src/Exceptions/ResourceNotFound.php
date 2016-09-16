<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class ResourceNotFound extends Exception
{
    /**
     * Setting config file is not found.
     *
     * @param  string $namespace
     *
     * @return static
     */
    public static function settingsConfigNotFound($namespace)
    {
        return new static("Class {$namespace} not found.");
    }
}
