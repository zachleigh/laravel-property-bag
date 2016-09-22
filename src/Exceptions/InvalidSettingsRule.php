<?php

namespace LaravelPropertyBag\Exceptions;

use Exception;

class InvalidSettingsRule extends Exception
{
    /**
     * Setting rule method can not be found.
     *
     * @param string $rule
     * @param string $method
     *
     * @return static
     */
    public static function ruleNotFound($rule, $method)
    {
        return new static(
            "Method {$method} for rule {$rule} not found. ".
            "Check rule spelling or create method {$method} in Rules.php."
        );
    }
}
