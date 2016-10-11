<?php

namespace LaravelPropertyBag\tests\Classes;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\Settings\HasSettings;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'max_members',
    ];

    /**
     * Settings config class.
     *
     * @var string
     */
    protected $settingsConfig = 'LaravelPropertyBag\tests\Classes\GroupConfig';
}
