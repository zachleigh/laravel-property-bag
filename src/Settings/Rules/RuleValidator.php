<?php

namespace LaravelPropertyBag\Settings\Rules;

use LaravelPropertyBag\Helpers\NameResolver;
use LaravelPropertyBag\Exceptions\InvalidSettingsRule;

class RuleValidator
{
    /**
     * Validate the value given for the rule.
     *
     * @param string $rule
     * @param mixed  $value
     *
     * @return bool|InvalidSettingsRule
     */
    public function validate($rule, $value)
    {
        $arguments = $this->buildArgumentArray($rule, $value);

        $method = $this->makeRuleMethod($rule);

        if ($this->userDefinedExists($method)) {
            $class = NameResolver::makeRulesFileName();

            return call_user_func_array([$class, $method], $arguments);
        } elseif (method_exists(Rules::class, $method)) {
            return call_user_func_array([Rules::class, $method], $arguments);
        }

        throw InvalidSettingsRule::ruleNotFound($rule, $method);
    }

    /**
     * String is a rule.
     *
     * @param string $string
     *
     * @return bool|string
     */
    public function isRule($string)
    {
        if ($isRule = preg_match('/:(.*?):/', $string, $match)) {
            return $match[1];
        }

        return (bool) $isRule;
    }

    /**
     * Make method name used to validate rule.
     *
     * @param string $rule
     *
     * @return string
     */
    protected function makeRuleMethod($rule)
    {
        if (strpos($rule, '=') !== false) {
            $rule = explode('=', $rule)[0];
        }

        return 'rule'.ucfirst($rule);
    }

    /**
     * User defined rule method exists.
     *
     * @param  string $method
     *
     * @return boolean
     */
    protected function userDefinedExists($method)
    {
        $userDefined = NameResolver::makeRulesFileName();

        return class_exists($userDefined) &&
            method_exists($userDefined, $method);
    }

    /**
     * Build argument array from rule and value.
     *
     * @param string $rule
     * @param mixed  $value
     *
     * @return array
     */
    protected function buildArgumentArray($rule, $value)
    {
        $argumentString = explode('=', $rule);

        $arguments = [$value];

        if (isset($argumentString[1])) {
            return array_merge($arguments, explode(',', $argumentString[1]));
        }

        return $arguments;
    }
}
