<?php

namespace LaravelPropertyBag\Traits;

use \Illuminate\Console\AppNamespaceDetectorTrait;

trait NameResolver
{
    use AppNamespaceDetectorTrait;

    /**
     * Get namespace for UserSettings file.
     *
     * @return string
     */
    protected function getUserSettingsNamespace()
    {
        $namespace = $this->getAppNamespace().'UserSettings\\UserSettings';

        if (class_exists($namespace)) {
            return $namespace;
        }

        return 'LaravelPropertyBag\Settings\UserSettings';
    }
}
