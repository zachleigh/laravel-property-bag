<?php

namespace LaravelPropertyBag\tests\Unit;

use LaravelPropertyBag\tests\TestCase;

class TypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_distinguishes_between_bool_and_int_types()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set(['test_settings3' => true]);

        $result = $settings->get('test_settings3');

        $this->assertTrue($result === true);

        $this->assertTrue($result !== 1);

        $settings->set(['test_settings3' => 1]);

        $result = $settings->get('test_settings3');

        $this->assertTrue($result === 1);

        $this->assertTrue($result !== true);
    }

    /**
     * @test
     */
    public function it_distinguishes_between_bool_and_string_types()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set(['test_settings3' => 'true']);

        $result = $settings->get('test_settings3');

        $this->assertTrue($result === 'true');

        $this->assertTrue($result !== true);

        $settings->set(['test_settings3' => false]);

        $result = $settings->get('test_settings3');

        $this->assertTrue($result === false);

        $this->assertTrue($result !== 'false');
    }

    /**
     * @test
     */
    public function it_distinguishes_between_int_and_string_types()
    {
        $this->actingAs($this->user);

        $settings = $this->user->settings();

        $settings->set(['test_settings3' => 1]);

        $result = $settings->get('test_settings3');

        $this->assertTrue($result === 1);

        $this->assertTrue($result !== '1');

        $settings->set(['test_settings3' => '0']);

        $result = $settings->get('test_settings3');

        $this->assertTrue($result === '0');

        $this->assertTrue($result !== 0);
    }
}
