<?php

namespace LaravelPropertyBag\tests;

use Hash;
use LaravelPropertyBag\ServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use LaravelPropertyBag\tests\Classes\User;
use LaravelPropertyBag\tests\Classes\Group;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use LaravelPropertyBag\LaravelPropertyBagServiceProvider;
use LaravelPropertyBag\tests\Migrations\CreateUsersTable;
use LaravelPropertyBag\tests\Migrations\CreateGroupsTable;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;
use LaravelPropertyBag\tests\Migrations\CreateGroupSettingsTable;

abstract class TestCase extends IlluminateTestCase
{
    use DatabaseTransactions;

    /**
     * Testing property bag register.
     *
     * @var Collection
     */
    protected $registered;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(ServiceProvider::class);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Setup DB and test variables before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');

        $this->app['config']->set(
            'database.connections.sqlite.database',
            ':memory:'
        );

        $this->migrate();

        $this->registered = require(__DIR__.'/Stubs/registered.php');
    }

    /**
     * Run migrations.
     */
    protected function migrate()
    {
        (new CreateUsersTable)->up();

        (new CreateGroupsTable)->up();

        (new CreateGroupSettingsTable)->up();

        require_once(
            __DIR__.
            '/../src/Migrations/2016_06_03_000000_create_user_settings_table.php'
        );

        $userSettingsTable = 'CreateUserSettingsTable';

        (new $userSettingsTable)->up();
    }

    /**
     * Make a user.
     *
     * @return User
     */
    protected function makeUser()
    {
        return User::create([
            'name' => 'Sam Wilson',
            'email' => 'samwilson@example.com',
            'password' => Hash::make('randomstring')
        ]);
    }

    /**
     * Make a group.
     *
     * @return Group
     */
    protected function makeGroup()
    {
        return Group::create([
            'name' => 'Laravel User Group',
            'type' => 'tech',
            'max_members' => 20
        ]);
    }
}
