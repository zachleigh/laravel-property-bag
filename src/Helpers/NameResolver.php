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
}
