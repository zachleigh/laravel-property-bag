<?php

namespace LaravelPropertyBag\Settings;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\Exceptions\InvalidSettingsValue;

class PropertyBag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'property_bag';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'resource_type',
        'resource_id',
        'key',
        'value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array'
    ];
}
