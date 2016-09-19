<?php

namespace LaravelPropertyBag\Settings;

class ResourceConfig
{
    /**
     * Return a collection of registered settings.
     *
     * @return Collection
     */
    public function registeredSettings()
    {
        return collect($this->registeredSettings);
    }
}
