<?php

namespace LaravelPropertyBag\Helpers;

use Illuminate\Container\Container;

class NameResolver
{
    /**
     * Get the app namespace from the container.
     *
     * @return string
     */
    public static function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

    /**
     * Make config file name for resource.
     *
     * @param string $resourceName
     *
     * @return string
     */
    public static function makeConfigFileName($resourceName)
    {
        $appNamespace = static::getAppNamespace();

        return $appNamespace.'Settings\\'.$resourceName.'Settings';
    }

    /**
     * Make rules file name.
     *
     * @return string
     */
    public static function makeRulesFileName()
    {
        $appNamespace = static::getAppNamespace();

        return $appNamespace.'Settings\\Resources\\Rules';
    }
}
