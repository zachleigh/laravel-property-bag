<?php

namespace LaravelPropertyBag\tests\Classes;

use Illuminate\Database\Eloquent\Model;
use LaravelPropertyBag\Settings\HasSettings;

class Post extends Model
{
    use HasSettings;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'user_id',
    ];

    /**
     * Settings config class.
     *
     * @var string
     */
    protected $settingsConfig = 'LaravelPropertyBag\tests\Classes\PostConfig';
}
