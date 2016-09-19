<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class ResourceNotFound extends Exception
{
    /**
     * Config file for resource can not be found.
     *
     * @param string $namespace
     *
     * @return static
     */
    public static function resourceConfigNotFound($namespace)
    {
        return new static("Class {$namespace} not found.");
    }
}
