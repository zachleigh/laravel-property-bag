<?php

namespace LaravelPropertyBag\Settings;

class ResourceConfig
{
    /**
     * Return a collection of registered settings.
     *
     * @return Collection
     */
    public function getRegisteredSettings()
    {
        return collect($this->registeredSettings);
    }
}
