<?php

namespace LaravelPropertyBag\Settings\Rules;

class Rules
{
    /**
     * Return true if value is alpha characters.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function ruleAlpha($value)
    {
        return ctype_alpha($value);
    }

    /**
     * Return true for everything.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function ruleAny($value)
    {
        return true;
    }

    /**
     * Return true if value is alpha characters.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function ruleAlphanum($value)
    {
        return ctype_alnum($value);
    }

    /**
     * Return true if value is alpha characters.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function ruleBool($value)
    {
        return is_bool($value);
    }

    /**
     * Return true if value is integer.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function ruleInt($value)
    {
        return is_int($value);
    }

    /**
     * Return true if value is numeric.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function ruleNum($value)
    {
        return is_numeric($value);
    }

    /**
     * Return true if value is numeric.
     *
     * @param mixed $value
     * @param num   $low
     * @param num   $high
     *
     * @return bool
     */
    public static function ruleRange($value, $low, $high)
    {
        return ($low <= $value) && ($value <= $high);
    }

    /**
     * Return true if value is a string.
     *
     * @param  mixed $value
     *
     * @return bool
     */
    public static function ruleString($value)
    {
        return is_string($value);
    }
}
