<?php

namespace LaravelPropertyBag\tests\Unit;

use LaravelPropertyBag\tests\TestCase;

class HasSettingsTest extends TestCase
{
    /**
     * @test
     */
    public function if_key_is_given_to_settings_method_value_is_returned()
    {
        $value = $this->user->settings('test_settings1');

        $this->assertEquals('monkey', $value);
    }

    /**
     * @test
     */
    public function if_array_is_given_to_settings_method_value_is_set()
    {
        $settings = [
            'test_settings1' => 'bananas',
        ];

        $this->user->settings($settings);

        $this->assertEquals(
            $settings,
            $this->user->settings()->allSaved()->all()
        );

        $this->seeInDatabase('property_bag', [
            'resource_id'   => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key'           => 'test_settings1',
            'value'         => json_encode('["bananas"]'),
        ]);
    }

    /**
     * @test
     */
    public function settings_can_be_set_with_from_hassettings()
    {
        $settings = [
            'test_settings1' => 'bananas',
            'test_settings3' => true,
        ];

        $this->user->setSettings($settings);

        $this->assertEquals(
            $settings,
            $this->user->settings()->allSaved()->all()
        );

        $this->seeInDatabase('property_bag', [
            'resource_id'   => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key'           => 'test_settings1',
            'value'         => json_encode('["bananas"]'),
        ]);

        $this->seeInDatabase('property_bag', [
            'resource_id'   => $this->user->id,
            'resource_type' => 'LaravelPropertyBag\tests\Classes\User',
            'key'           => 'test_settings3',
            'value'         => json_encode('[true]'),
        ]);
    }

    /**
     * @test
     */
    public function all_settings_can_be_retrieved_from_hassettings()
    {
        $settings = [
            'test_settings1' => 'bananas',
            'test_settings3' => true,
        ];

        $this->user->setSettings($settings);

        $settings['test_settings2'] = true;

        $this->assertEquals(
            $settings,
            $this->user->allSettings()->all()
        );
    }

    /**
     * @test
     */
    public function default_setting_can_be_retrieved_from_hassettings()
    {
        $default = $this->user->defaultSetting('test_settings1');

        $this->assertEquals('monkey', $default);
    }

    /**
     * @test
     */
    public function all_defaults_can_be_retrieved_from_hassettings()
    {
        $defaults = $this->user->defaultSetting();

        $this->assertEquals([
            'test_settings1' => 'monkey',
            'test_settings2' => true,
            'test_settings3' => false,
        ], $defaults->all());
    }

    /**
     * @test
     */
    public function allowed_settings_for_single_key_can_be_retrieved_from_hassettings()
    {
        $allowed = $this->user->allowedSetting('test_settings1');

        $this->assertEquals(['bananas', 'grapes', 8, 'monkey'], $allowed->all());
    }

    /**
     * @test
     */
    public function all_allowed_values_can_be_retrieved_from_hassettings()
    {
        $allowed = $this->user->allowedSetting();

        $actual = [
            'test_settings1' => ['bananas', 'grapes', 8, 'monkey'],
            'test_settings2' => [true, false],
            'test_settings3' => [true, false, 'true', 'false', 0, 1, '0', '1'],
        ];

        $this->assertEquals($actual, $allowed->all());
    }

    /**
     * @test
     */
    public function all_resources_with_setting_can_be_retrieved_from_hassettings()
    {
        $collection = $this->user::withSetting('test_settings1');

        $this->assertCount(1, $collection);
    }

    /**
     * @test
     */
    public function all_resources_with_setting_and_value_can_be_retrieved_from_hassettings()
    {
        $collection = $this->user::withSetting('test_settings1', 'monkey');

        $this->assertCount(1, $collection);
    }
}
