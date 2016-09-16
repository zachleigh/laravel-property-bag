<?php

namespace LaravelPropertyBag\tests\Classes;

use App\User as BaseUser;
use LaravelPropertyBag\Settings\HasSettings;

class User extends BaseUser
{
    use HasSettings;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Settings config class.
     *
     * @var string
     */
    protected $settingsConfig = 'LaravelPropertyBag\tests\Classes\UserConfig';
}
