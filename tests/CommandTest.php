<?php

namespace LaravelPropertyBag\tests;

use File;
use Artisan;

class CommandTest  extends TestCase
{
    /**
     * @test
     */
    public function publish_user_command_creates_settings_and_propertybag_files()
    {
        $this->assertFileNotExists(
            __DIR__.'/../vendor/laravel/laravel/app/UserSettings/UserSettings.php'
        );

        $this->assertFileNotExists(
            __DIR__.'/../vendor/laravel/laravel/app/UserSettings/UserPropertyBag.php'
        );

        Artisan::call('lpb:publish-user');

        $this->assertFileExists(
            __DIR__.'/../vendor/laravel/laravel/app/UserSettings/UserSettings.php'
        );

        $this->assertFileExists(
            __DIR__.'/../vendor/laravel/laravel/app/UserSettings/UserPropertyBag.php'
        );

        File::deleteDirectory(
            __DIR__.'/../vendor/laravel/laravel/app/UserSettings'
        );
    }
}
