<?php

namespace LaravelPropertyBag\tests;

use Illuminate\Support\Collection;
use LaravelPropertyBag\UserSettings\UserSettings;

class PropertyBagTest extends TestCase
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

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user->id(),
            'key' => 'test_settings2',
            'value' => json_encode('[true]')
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

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user->id(),
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
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

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user->id(),
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->assertContains('test_settings2', $settings->all());

        $this->assertEquals($settings->get('test_settings2'), true);

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user->id(),
            'key' => 'test_settings2',
            'value' => json_encode('[true]')
        ]);
    }

    /**
     * @test
     */
    public function only_changed_settings_are_updated()
    {
        // TODO
    }

    /**
     * @test
     */
    public function settings_on_the_object_match_the_settings_in_the_database()
    {
        // TODO
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

        $settings = $group->settings();

        $this->assertEmpty($settings->all());

        $settings->set([
            'test_settings1' => 'monkey',
            'test_settings2' => true,
        ]);

        $settings->set([
            'test_settings1' => 'grapes',
            'test_settings2' => false,
        ]);

        $this->assertContains('grapes', $settings->all());

        $this->assertEquals($settings->get('test_settings1'), 'grapes');

        $this->seeInDatabase('group_settings', [
            'group_id' => $group->id(),
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->assertContains(false, $settings->all());

        $this->assertEquals($settings->get('test_settings2'), false);

        $this->seeInDatabase('group_settings', [
            'group_id' => $group->id(),
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
        ]);
    }

    /**
     * @test
     */
    public function settings_can_be_registered_on_settings_class()
    {
        $group = $this->makeGroup();

        $settings = $group->settings();

        $this->assertTrue($settings->isRegistered('test_settings1'));
    }

    /**
     * @test
     */
    public function settings_intsance_is_persisted_on_resource_model()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $result = $user->settings($this->registered)->get('test_settings1');

        $result = $user->settings()->get('test_settings1');

        $this->assertEquals('monkey', $result);
    }

    /**
     * @test
     */
    public function local_settings_are_always_synced_with_database()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $this->assertEmpty($settings->all());

        $this->assertEmpty($user->allSettingsFlat()->all());

        $test1 = [
            'test_settings1' => 'monkey',
            'test_settings2' => false
        ];

        $settings->set($test1);

        $this->assertEquals($settings->all(), $test1);

        $this->assertEquals($user->allSettingsFlat()->all(), $test1);

        $test2 = [
            'test_settings3' => 'true',
            'test_settings1' => 'bananas'
        ];

        $settings->set($test2);

        $test2['test_settings2'] = false;

        $this->assertEquals($settings->all(), $test2);

        $this->assertEquals($user->allSettingsFlat()->all(), $test2);

        $test3 = [
            'test_settings3' => 0,
            'test_settings1' => 8,
            'test_settings2' => false
        ];

        $settings->set($test3);

        $this->assertEquals($settings->all(), $test3);

        $this->assertEquals($user->allSettingsFlat()->all(), $test3);
    }

    /**
     * @test
     */
    public function if_default_value_is_set_database_entry_is_deleted()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $settings = $user->settings($this->registered);

        $settings->set([
            'test_settings1' => 'grapes'
        ]);

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user->id(),
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $settings->set([
            'test_settings1' => 'monkey'
        ]);

        $this->dontSeeInDatabase('user_property_bag', [
            'user_id' => $user->id(),
            'key' => 'test_settings1',
            'value' => json_encode('["monkey"]')
        ]);
    }
}
