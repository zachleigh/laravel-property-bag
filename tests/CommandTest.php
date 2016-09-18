<?php

namespace LaravelPropertyBag\tests;

use File;
use Artisan;

class CommandTest extends TestCase
{
    /**
     * @test
     */
    public function publish_user_command_creates_settings_and_propertybag_files()
    {
        $this->assertFileNotExists(
            __DIR__.'/../vendor/laravel/laravel/app/Settings/UserSettings.php'
        );

        Artisan::call('pbag:make', ['resource' => 'User']);

        $this->assertFileExists(
            __DIR__.'/../vendor/laravel/laravel/app/Settings/UserSettings.php'
        );

        File::deleteDirectory(
            __DIR__.'/../vendor/laravel/laravel/app/Settings'
        );
    }
}
