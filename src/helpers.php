<?php

use LaravelPropertyBag\Helpers\NameResolver;

/**
 * Settings function for user settings.
 */
if (!function_exists('settings')) {
    function settings($key = null)
    {
        $namespace = NameResolver::getUserSettingsNamespace();
        
        $settings = app($namespace);

        return $key ? $settings->get($key) : $settings;
    }
}