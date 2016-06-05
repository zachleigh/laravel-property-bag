<?php

namespace LaravelPropertyBag\tests;

use Illuminate\Support\Collection;
use LaravelPropertyBag\User\UserSettings;

class SettingsTest extends TestCase
{
    /**
     * @test
     */
    public function a_user_can_access_the_settings_object()
    {
        $user = $this->makeUser();

        $settings = $user->settings();

        $this->assertInstanceOf(UserSettings::class, $settings);
    }

    /**
     * @test
     */
    public function settings_can_be_accessed_from_the_helper_function()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = settings();

        $this->assertInstanceOf(UserSettings::class, $settings);
    }

    /**
     * @test
     */
    public function a_valid_setting_key_value_pair_passes_validation()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $result = $settings->isValid('test_settings1', 'bananas');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function an_invalid_setting_key_fails_validation()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $result = $settings->isValid('fake', true);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function an_invalid_setting_value_fails_validation()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $result = $settings->isValid('test_settings2', 'ok');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function adding_a_new_setting_creates_a_new_user_setting_record()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $this->assertEmpty($settings->all());

        $settings->set(['test_settings2' => true]);

        $this->assertContains('test_settings2', $settings->all());

        $this->assertEquals($settings->get('test_settings2'), true);

        $this->seeInDatabase('user_settings', [
            'user_id' => $user->id(),
            'key' => 'test_settings2',
            'value' => 1
        ]);
    }

    /**
     * @test
     */
    public function updating_a_setting_updates_the_setting()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $settings->set(['test_settings2' => true]);

        $settings->set(['test_settings2' => false]);

        $this->assertEquals($settings->get('test_settings2'), false);

        $this->seeInDatabase('user_settings', [
            'user_id' => $user->id(),
            'key' => 'test_settings2',
            'value' => 0
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_set_many_settings_at_once()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $this->assertEmpty($settings->all());

        $settings->set([
            'test_settings1' => 'grapes',
            'test_settings2' => true,
        ]);

        $this->assertContains('test_settings1', $settings->all());

        $this->assertEquals($settings->get('test_settings1'), 'grapes');

        $this->seeInDatabase('user_settings', [
            'user_id' => $user->id(),
            'key' => 'test_settings1',
            'value' => 'grapes'
        ]);

        $this->assertContains('test_settings2', $settings->all());

        $this->assertEquals($settings->get('test_settings2'), true);

        $this->seeInDatabase('user_settings', [
            'user_id' => $user->id(),
            'key' => 'test_settings2',
            'value' => 1
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_get_a_setting()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $user->settings($this->registered)->set(['test_settings2' => true]);

        $result = $user->settings()->get('test_settings2');

        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function a_user_can_get_a_setting_from_the_global_helper()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $user->settings($this->registered)->set(['test_settings2' => true]);

        $result = settings()->get('test_settings2');

        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function if_the_setting_is_not_set_the_default_value_is_returned()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $result = $user->settings($this->registered)->get('test_settings1');

        $this->assertEquals('monkey', $result);
    }

    /**
     * @test
     */
    public function a_user_can_not_get_an_invalid_setting()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $result = $user->settings($this->registered)->get('invalid_setting');

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function a_resource_can_access_and_use_the_property_bag()
    {
        $group = $this->makeGroup();

        $settings = $group->settings($this->registered);

        $this->assertEmpty($settings->all());

        $settings->set([
            'test_settings1' => 'monkeys',
            'test_settings2' => false,
        ]);

        $settings->set([
            'test_settings1' => 'grapes',
            'test_settings2' => true,
        ]);

        $this->assertContains('test_settings1', $settings->all());

        $this->assertEquals($settings->get('test_settings1'), 'grapes');

        $this->seeInDatabase('group_settings', [
            'group_id' => $group->id(),
            'key' => 'test_settings1',
            'value' => 'grapes'
        ]);

        $this->assertContains('test_settings2', $settings->all());

        $this->assertEquals($settings->get('test_settings2'), true);

        $this->seeInDatabase('group_settings', [
            'group_id' => $group->id(),
            'key' => 'test_settings2',
            'value' => 1
        ]);
    }
}
