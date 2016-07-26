<?php

namespace LaravelPropertyBag\tests;

use Illuminate\Support\Collection;
use LaravelPropertyBag\UserSettings\UserSettings;

class PropertyBagTest extends TestCase
{
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
            'group_id' => $group->resourceId(),
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->assertContains(false, $settings->all());

        $this->assertEquals($settings->get('test_settings2'), false);

        $this->seeInDatabase('group_settings', [
            'group_id' => $group->resourceId(),
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
        ]);
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
            'test_settings1' => 'bananas',
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
    public function settings_are_set_per_user()
    {
        $user1 = $this->makeUser();

        $user2 = $this->makeUser('Bob Sanchez', 'bob@example.com');

        $user3 = $this->makeUser('Sally Smith', 'sally@example.com');

        $this->actingAs($user1);

        $settings = $user1->settings($this->registered);

        $settings->set([
            'test_settings1' => 'grapes'
        ]);

        $this->assertEquals($settings->all(), ['test_settings1' => 'grapes']);

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user1->resourceId(),
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->actingAs($user2);

        $settings = $user2->settings($this->registered);

        $settings->set([
            'test_settings1' => 8
        ]);

        $this->assertEquals($settings->all(), ['test_settings1' => 8]);

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user2->resourceId(),
            'key' => 'test_settings1',
            'value' => json_encode('[8]')
        ]);

        $this->actingAs($user3);

        $settings = $user3->settings($this->registered);

        $settings->set([
            'test_settings1' => 'bananas'
        ]);

        $this->assertEquals($settings->all(), ['test_settings1' => 'bananas']);

        $this->seeInDatabase('user_property_bag', [
            'user_id' => $user3->resourceId(),
            'key' => 'test_settings1',
            'value' => json_encode('["bananas"]')
        ]);
    }
}
