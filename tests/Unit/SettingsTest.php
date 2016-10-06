<?php

namespace LaravelPropertyBag\tests\Unit;

use Illuminate\Support\Collection;
use LaravelPropertyBag\tests\TestCase;
use LaravelPropertyBag\Settings\Settings;

class SettingsTest extends TestCase
{
    /**
     * @test
     */
    public function a_resource_can_access_the_settings_object()
    {
        $this->assertInstanceOf(Settings::class, $this->user->settings());
    }

    /**
     * @test
     *
     * @expectedException LaravelPropertyBag\Exceptions\ResourceNotFound
     * @expectedExceptionMessage Class App\Settings\AdminSettings not found.
     */
    public function exception_is_thrown_when_config_file_not_found()
    {
        $this->makeAdmin()->settings();
    }

    /**
     * @test
     */
    public function settings_class_has_registered_settings()
    {
        $registered = $this->user->settings()->getRegistered();

        $this->assertInstanceOf(Collection::class, $registered);

        $this->assertCount(17, $registered->flatten());
    }

    /**
     * @test
     */
    public function settings_class_can_check_for_registered_settings()
    {
        $group = $this->makeGroup();

        $settings = $group->settings();

        $this->assertTrue($settings->isRegistered('test_settings1'));
    }

    /**
     * @test
     */
    public function a_valid_setting_key_value_pair_passes_validation()
    {
        $result = $this->user->settings()->isValid('test_settings1', 'bananas');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function an_invalid_setting_key_fails_validation()
    {
        $result = $this->user->settings()->isValid('fake', true);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function an_invalid_setting_value_fails_validation()
    {
        $result = $this->user->settings()->isValid('test_settings2', 'ok');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function a_default_value_can_de_detected()
    {
        $result = $this->user->settings()->isDefault('test_settings3', false);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function a_non_default_value_can_de_detected()
    {
        $result = $this->user->settings()->isDefault('test_settings3', true);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function a_resource_can_get_the_default_value()
    {
        $default = $this->user->settings()->getDefault('test_settings1');

        $this->assertEquals('monkey', $default);
    }

    /**
     * @test
     */
    public function a_resource_can_get_all_the_default_values()
    {
        $defaults = $this->user->settings()->allDefaults();

        $this->assertEquals([
            "test_settings1" => "monkey",
            "test_settings2" => true,
            "test_settings3" => false
        ], $defaults->all());
    }

    /**
     * @test
     */
    public function a_resource_can_get_the_allowed_values()
    {
        $allowed = $this->user->settings()->getAllowed('test_settings1');

        $this->assertEquals(['bananas', 'grapes', 8, 'monkey'], $allowed->all());
    }

    /**
     * @test
     */
    public function a_resource_can_get_all_allowed_values()
    {
        $allowed = $this->user->settings()->allAllowed()->flatten();

        $this->assertCount(14, $allowed);
    }

    /**
     * @test
     */
    public function adding_a_new_setting_creates_a_new_user_setting_record()
    {
        $this->user->settings()->set(['test_settings3' => true]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'value' => json_encode('[true]')
        ]);
    }

    /**
     * @test
     */
    public function adding_a_new_setting_refreshes_settings_on_object()
    {
        $this->assertEmpty($this->user->settings()->allSaved());

        $this->actingAs($this->user);

        $this->user->settings()->set(['test_settings3' => true]);
        
        $this->assertEquals(
            ['test_settings3' => true],
            $this->user->settings()->allSaved()->all()
        );
    }

    /**
     * @test
     */
    public function updating_a_setting_updates_the_setting_record()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set(['test_settings1' => 'bananas']);

        $this->assertEquals(
            ['test_settings1' => 'bananas'],
            $settings->allSaved()->all()
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["bananas"]')
        ]);

        $settings->set(['test_settings1' => 'grapes']);

        $this->assertEquals(
            ['test_settings1' => 'grapes'],
            $settings->allSaved()->all()
        );

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_set_many_settings_at_once()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $this->assertEmpty($settings->allSaved());

        $test = [
            'test_settings1' => 'grapes',
            'test_settings2' => false,
        ];

        $settings->set($test);

        $this->assertEquals($test, $settings->allSaved()->all());

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings2',
            'value' => json_encode('[false]')
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_get_a_setting()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set(['test_settings2' => false]);

        $result = $settings->get('test_settings2');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function if_the_setting_is_not_set_the_default_value_is_returned()
    {
        $this->actingAs($this->user);

        $result = $this->user->settings()->get('test_settings1');

        $this->assertEquals('monkey', $result);
    }

    /**
     * @test
     */
    public function a_user_can_get_all_the_settings_being_used()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set([
            'test_settings1' => 'bananas'
        ]);

        $this->assertEquals([
            'test_settings1' => 'bananas',
            'test_settings2' => true,
            'test_settings3' => false
        ], $this->user->settings()->all()->all());
    }

    /**
     * @test
     */
    public function a_user_can_get_all_the_settings_saved_in_the_database()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set([
            'test_settings1' => 'bananas'
        ]);

        $this->assertEquals([
            'test_settings1' => 'bananas'
        ], $this->user->settings()->allSaved()->all());
    }

    /**
     * @test
     */
    public function a_user_can_not_get_an_invalid_setting()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $result = $settings->get('invalid_setting');

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function if_default_value_is_set_database_entry_is_deleted()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set([
            'test_settings1' => 'grapes'
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'key' => 'test_settings1',
            'value' => json_encode('["grapes"]')
        ]);

        $settings->set([
            'test_settings1' => 'monkey'
        ]);

        $this->dontSeeInDatabase('property_bag', [
            'resource_id' => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key' => 'test_settings1',
            'value' => json_encode('["monkey"]')
        ]);
    }

    /**
     * @test
     *
     * @expectedException LaravelPropertyBag\Exceptions\InvalidSettingsValue
     * @expectedExceptionMessage Given value is not a registered allowed value for test_settings1.
     */
    public function setting_an_unallowed_setting_value_throws_exception()
    {
        $this->actingAs($this->user);

        $this->user->settings()->set([
            'test_settings1' => 'invalid'
        ]);
    }

    /**
     * @test
     */
    public function settings_can_be_registered_in_config_file_method()
    {
        $post = $this->makePost();

        $defaults = $post->defaultSetting();

        $this->assertEquals([
            "test_settings1" => "monkey",
            "test_settings2" => true,
            "test_settings3" => false
        ], $defaults->all());

        $allowed = $post->allowedSetting();

        $actual = [
            'test_settings1' => ['bananas', 'grapes', 8, 'monkey',],
            'test_settings2' => [true, false],
            'test_settings3' => [true, false, 'true', 'false', 0, 1, '0', '1']
        ];

        $this->assertEquals($actual, $allowed->all());
    }

    /**
     * @test
     */
    public function settings_with_allowed_rule_can_be_set()
    {
        $comment = $this->makeComment();

        $settings = [
            'alpha' => 'abc',
            'alphanum' => 'abc123',
            'any' => 45,
            'bool' => false,
            'integer' => 10,
            'numeric' => '87',
            'range' => 4,
            'range2' => -1,
            'string' => 'test'
        ];

        $comment->settings()->set($settings);

        $settings['invalid'] = null;
        $settings['user_defined'] = true;

        $this->assertEquals($settings, $comment->settings()->all()->all());
    }

    /**
     * @test
     *
     * @expectedException LaravelPropertyBag\Exceptions\InvalidSettingsValue
     * @expectedExceptionMessage Given value is not a registered allowed value for alpha.
     */
    public function settings_with_invalid_rule_values_can_not_be_set()
    {
        $comment = $this->makeComment();

        $comment->settings()->set(['alpha' => 4]);
    }
}
