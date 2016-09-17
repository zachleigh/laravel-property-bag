<?php

namespace LaravelPropertyBag\Settings;

class ResourceConfig
{
    /**
     * Return collection of registered settings.
     *
     * @return Collection
     */
    public function getRegisteredSettings()
    {
        return collect($this->registeredSettings);
    }
}
