<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class InvalidSettingsValue extends Exception
{
    /**
     * Failed key name.
     *
     * @var string
     */
    protected $failedKey;

    /**
     * Setting value is not definied in key's allowed values array.
     *
     * @param string $key
     *
     * @return static
     */
    public static function settingNotAllowed($key)
    {
        $exception = new static("Given value is not a registered allowed value for {$key}.");

        return $exception->setFailedKey($key);
    }

    /**
     * Sets failed key name.
     *
     * @param string $key
     *
     * @return static
     */
    public function setFailedKey($key)
    {
        $this->failedKey = $key;

        return $this;
    }

    /**
     * Returns failed key name.
     *
     * @return string
     */
    public function getFailedKey()
    {
        return $this->failedKey;
    }
}
