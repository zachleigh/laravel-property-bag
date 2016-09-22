<?php

namespace LaravelPropertyBag\tests\Functional;

use Illuminate\Support\Collection;
use LaravelPropertyBag\tests\TestCase;
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
            'test_settings1' => 'grapes',
            'test_settings2' => false,
        ]);

        $this->assertContains('grapes', $settings->all());

        $this->assertEquals('grapes', $settings->get('test_settings1'));

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->assertContains(false, $settings->all());

        $this->assertEquals(false, $settings->get('test_settings2'));

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
        ]);

        $settings->set([
            'test_settings1' => 'bananas',
            'test_settings2' => true,
            'test_settings3' => 'false'
        ]);

        $this->assertContains('bananas', $settings->all());

        $this->assertEquals('bananas', $settings->get('test_settings1'));

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings1',
            'value' => json_encode('["bananas"]')
        ]);

        $this->assertContains(true, $settings->all());

        $this->assertEquals(true, $settings->get('test_settings2'));

        $this->dontSeeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings2'
        ]);

        $this->assertContains('false', $settings->all());

        $this->assertEquals('false', $settings->get('test_settings3'));

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings3',
            'value' => json_encode('["false"]')
        ]);
    }

    /**
     * @test
     */
    public function multiple_instances_of_same_resource_can_use_settings()
    {
        $user1 = $this->user;

        $user2 = $this->makeUser('Bob Sanchez', 'bob@example.com');

        $user3 = $this->makeUser('Sally Smith', 'sally@example.com');

        $user1->settings()->set([
            'test_settings1' => 'grapes'
        ]);

        $this->assertEquals(
            ['test_settings1' => 'grapes'],
            $user1->settings()->allSaved()->all()
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
            ['test_settings1' => 8],
            $user2->settings()->allSaved()->all()
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
            ['test_settings1' => 'bananas'],
            $user3->settings()->allSaved()->all()

        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $user3->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["bananas"]')
        ]);

        // Make sure the first one isn't being overwritten
        $this->assertEquals(
            ['test_settings1' => 'grapes'],
            $user1->settings()->allSaved()->all()
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $user1->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);
    }

    /**
     * @test
     */
    public function multiple_resources_can_use_settings()
    {
        $group = $this->makeGroup();

        $groupSettings = [
            'test_settings1' => 8,
            'test_settings2' => true,
            'test_settings3' => '0'
        ];

        $group->settings()->set($groupSettings);

        $userSettings = [
            'test_settings1' => 'monkey',
            'test_settings2' => false,
            'test_settings3' => 1
        ];

        $this->user->settings()->set($userSettings);

        $this->assertEquals(
            $groupSettings,
            $group->settings()->all()->all()
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings1',
            'value' => json_encode('[8]')
        ]);

        $this->dontSeeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings2'
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $group->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Group',
            'key' => 'test_settings3',
            'value' => json_encode('["0"]')
        ]);

        $this->assertEquals(
            $userSettings,
            $this->user->settings()->all()->all()
        );

        $this->dontSeeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1'
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings3',
            'value' => json_encode('[1]')
        ]);

        $comment = $this->makeComment();

        $comment->setSettings([
            'numeric' => 10,
            'bool' => true
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $comment->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Comment',
            'key' => 'numeric',
            'value' => json_encode('[10]')
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $comment->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\Comment',
            'key' => 'bool',
            'value' => json_encode('[true]')
        ]);
    }
}
