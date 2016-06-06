<?php

namespace LaravelPropertyBag\tests;

use Illuminate\Support\Collection;
use LaravelPropertyBag\UserSettings\UserSettings;

class TypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_distinguishes_between_bool_and_int_types()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $user->settings($this->registered)->set(['test_settings3' => true]);

        $result = settings()->get('test_settings3');

        $this->assertTrue($result === true);

        $this->assertTrue($result !== 1);

        $user->settings($this->registered)->set(['test_settings3' => 1]);

        $result = settings()->get('test_settings3');

        $this->assertTrue($result === 1);

        $this->assertTrue($result !== true);
    }

    /**
     * @test
     */
    public function it_distinguishes_between_bool_and_string_types()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $user->settings($this->registered)->set(['test_settings3' => 'true']);

        $result = settings()->get('test_settings3');

        $this->assertTrue($result === 'true');

        $this->assertTrue($result !== true);

        $user->settings($this->registered)->set(['test_settings3' => false]);

        $result = settings()->get('test_settings3');

        $this->assertTrue($result === false);

        $this->assertTrue($result !== 'false');
    }

    /**
     * @test
     */
    public function it_distinguishes_between_int_and_string_types()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $user->settings($this->registered)->set(['test_settings3' => 1]);

        $result = settings()->get('test_settings3');

        $this->assertTrue($result === 1);

        $this->assertTrue($result !== '1');

        $user->settings($this->registered)->set(['test_settings3' => '0']);

        $result = settings()->get('test_settings3');

        $this->assertTrue($result === '0');

        $this->assertTrue($result !== 0);
    }
}
