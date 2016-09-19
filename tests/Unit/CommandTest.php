<?php

namespace LaravelPropertyBag\tests\Unit;

use File;
use Artisan;
use LaravelPropertyBag\tests\TestCase;

class CommandTest extends TestCase
{
    /**
     * @test
     */
    public function publish_user_command_creates_settings_file()
    {
        $this->assertFileNotExists(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings/UserSettings.php'
        );

        Artisan::call('pbag:make', ['resource' => 'User']);

        $this->assertFileExists(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings/UserSettings.php'
        );

        File::deleteDirectory(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings'
        );
    }

    /**
     * @test
     */
    public function published_settings_file_has_correct_namespace()
    {
        Artisan::call('pbag:make', ['resource' => 'User']);

        $file = file_get_contents(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings/UserSettings.php'
        );

        $this->assertTrue(strrpos($file, 'namespace App\Settings;') !== false);

        File::deleteDirectory(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings'
        );
    }

    /**
     * @test
     */
    public function published_settings_file_has_correct_name()
    {
        Artisan::call('pbag:make', ['resource' => 'User']);

        $file = file_get_contents(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings/UserSettings.php'
        );

        $this->assertTrue(strrpos($file, 'UserSettings') !== false);

        File::deleteDirectory(
            __DIR__.'/../../vendor/laravel/laravel/app/Settings'
        );
    }
}
