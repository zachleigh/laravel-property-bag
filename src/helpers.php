<?php

/**
 * Settings function for user settings.
 */
if (!function_exists('settings')) {
    function settings($key = null)
    {
        $settings = app('LaravelPropertyBag\Settings\UserSettings');

        return $key ? $settings->get($key) : $settings;
    }
}
