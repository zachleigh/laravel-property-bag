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
        $this->assertFileNotExists(app_path('Settings/UserSettings.php'));

        Artisan::call('pbag:make', ['resource' => 'User']);

        $this->assertFileExists(app_path('Settings/UserSettings.php'));

        File::deleteDirectory(app_path('Settings'));
    }

    /**
     * @test
     */
    public function published_settings_file_has_correct_namespace()
    {
        Artisan::call('pbag:make', ['resource' => 'User']);

        $file = file_get_contents(app_path('Settings/UserSettings.php'));

        $this->assertTrue(strrpos($file, 'namespace App\Settings;') !== false);

        File::deleteDirectory(app_path('Settings'));
    }

    /**
     * @test
     */
    public function published_settings_file_has_correct_name()
    {
        Artisan::call('pbag:make', ['resource' => 'User']);

        $file = file_get_contents(app_path('Settings/UserSettings.php'));

        $this->assertTrue(strrpos($file, 'UserSettings') !== false);

        File::deleteDirectory(app_path('Settings'));
    }

    /**
     * @test
     */
    public function publish_rules_file_creates_rules_file()
    {
        $this->assertFileNotExists(app_path('Settings/Resources/Rules.php'));

        Artisan::call('pbag:rules');

        $this->assertFileExists(app_path('Settings/Resources/Rules.php'));

        File::deleteDirectory(app_path('Settings'));
    }
}
