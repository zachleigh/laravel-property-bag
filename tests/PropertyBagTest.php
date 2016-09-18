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

        $this->assertEmpty($settings->allSaved());

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

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->assertContains(false, $settings->all());

        $this->assertEquals($settings->get('test_settings2'), false);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
        ]);

        // add some more settings manipulation
    }

    /**
     * @test
     */
    public function settings_are_set_per_resource_instance()
    {
        $user1 = $this->user;

        $user2 = $this->makeUser('Bob Sanchez', 'bob@example.com');

        $user3 = $this->makeUser('Sally Smith', 'sally@example.com');

        $user1->settings()->set([
            'test_settings1' => 'grapes'
        ]);

        $this->assertEquals(
            $user1->settings()->allSaved()->all(),
            ['test_settings1' => 'grapes']
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $user1->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $user2->settings()->set([
            'test_settings1' => 8
        ]);

        $this->assertEquals(
            $user2->settings()->allSaved()->all(),
            ['test_settings1' => 8]
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $user2->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('[8]')
        ]);

        $user3->settings()->set([
            'test_settings1' => 'bananas'
        ]);

        $this->assertEquals(
            $user3->settings()->allSaved()->all(),
            ['test_settings1' => 'bananas']
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $user3->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["bananas"]')
        ]);
    }

    /**
     * @test
     */
    public function multiple_resources_can_use_settings()
    {
        
    }
}
