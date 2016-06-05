<?php

namespace LaravelPropertyBag\tests\Classes;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\Settings\HasSettings;
use LaravelPropertyBag\tests\Classes\GroupSettings;

class Group extends Model
{
    use HasSettings;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * Settings class for the resource.
     *
     * @var string
     */
    protected $settingsClass = GroupSettings::class;

    /**
     * Property bag model for the resource.
     *
     * @var string
     */
    protected $propertyBagClass = GroupPropertyBag::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'max_members'
    ];
}
