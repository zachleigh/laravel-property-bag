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
    private $resource;

    /**
     * Construct.
     *
     * @param Model $resource
     */
    function __construct(Model $resource)
    {
        $this->resource = $resource;
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
