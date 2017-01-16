<?php

namespace LaravelPropertyBag\Settings;

use Illuminate\Database\Eloquent\Model;

class ResourceConfig
{
    /**
     * Resource that has settings.
     *
     * @var Model
     */
    protected $resource;

    /**
     * Sets resource.
     *
     * @param Model $resource.
     *
     * @return this
     */
    public function setResource(Model $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Returns resource.
     *
     * @return Model
     */
    public function getResource()
    {
        return $this->resource;
    }

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
