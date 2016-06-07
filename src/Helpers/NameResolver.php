<?php

namespace LaravelPropertyBag\Helpers;

use Illuminate\Container\Container;

class NameResolver
{
    /**
     * Get namespace for UserSettings file.
     *
     * @return string
     */
    public static function getUserSettingsNamespace()
    {
        $appNamespace = Container::getInstance()->getNamespace();

        $namespace = $appNamespace.'UserSettings\\UserSettings';

        if (class_exists($namespace)) {
            return $namespace;
        }

        return 'LaravelPropertyBag\Settings\UserSettings';
    }

    /**
     * Get the app namespace from the container.
     *
     * @return string
     */
    public static function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }
}
