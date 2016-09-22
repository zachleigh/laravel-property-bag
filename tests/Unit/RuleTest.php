<?php

namespace LaravelPropertyBag\tests\Unit;

use LaravelPropertyBag\tests\TestCase;
use LaravelPropertyBag\Settings\Rules\RuleValidator;

class RuleTest extends TestCase
{
    /**
     * @test
     */
    public function rule_validator_can_correctly_identify_rules()
    {
        $validator = new RuleValidator();

        $this->assertEquals('test', $validator->isRule(':test:'));

        $this->assertEquals(
            'test=arg1,arg2',
            $validator->isRule(':test=arg1,arg2:')
        );

        $this->assertFalse($validator->isRule('test'));

        $this->assertFalse($validator->isRule(':test'));

        $this->assertFalse($validator->isRule('test:'));
    }

    /**
     * @test
     *
     * @expectedException LaravelPropertyBag\Exceptions\InvalidSettingsRule
     * @expectedExceptionMessage Method ruleNope for rule nope not found. Check rule spelling or create method ruleNope in Rules.php.
     */
    public function throws_exception_for_rule_not_declared()
    {
        $this->makeComment()->settings()->isValid('invalid', 'test');
    }

    /**
     * @test
     */
    public function any_rule_returns_true_for_any()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('any', 7)
        );
    }

    /**
     * @test
     */
    public function alpha_rule_returns_true_for_alpha()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('alpha', 'alpha')
        );
    }

    /**
     * @test
     */
    public function alpha_rule_returns_false_for_non_alpha()
    {
        $this->assertFalse(
            $this->makeComment()->settings()->isValid('alpha', false)
        );
    }

    /**
     * @test
     */
    public function alphanum_rule_returns_true_for_alphanum()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('alphanum', 'alpha6')
        );
    }

    /**
     * @test
     */
    public function alphanum_rule_returns_false_for_non_alphanum()
    {
        $this->assertFalse(
            $this->makeComment()->settings()->isValid('alphanum', false)
        );
    }

    /**
     * @test
     */
    public function bool_rule_returns_true_for_bool()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('bool', true)
        );
    }

    /**
     * @test
     */
    public function bool_rule_returns_false_for_non_bool()
    {
        $this->assertFalse(
            $this->makeComment()->settings()->isValid('bool', 0)
        );
    }

    /**
     * @test
     */
    public function integer_rule_returns_true_for_integer()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('integer', 7)
        );
    }

    /**
     * @test
     */
    public function integer_rule_returns_false_for_non_integer()
    {
        $this->assertFalse(
            $this->makeComment()->settings()->isValid('integer', '7')
        );
    }

    /**
     * @test
     */
    public function numeric_rule_returns_true_for_numeric()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('numeric', '7')
        );
    }

    /**
     * @test
     */
    public function numeric_rule_returns_false_for_non_numeric()
    {
        $this->assertFalse(
            $this->makeComment()->settings()->isValid('numeric', 'test')
        );
    }

    /**
     * @test
     */
    public function range_rule_returns_true_for_value_in_range()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('range', '3')
        );
    }

    /**
     * @test
     */
    public function range_rule_returns_true_for_value_at_low_end()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('range', '1')
        );
    }

    /**
     * @test
     */
    public function range_rule_returns_true_for_value_at_high_end()
    {
        $this->assertTrue(
            $this->makeComment()->settings()->isValid('range', 5)
        );
    }

    /**
     * @test
     */
    public function range_rule_returns_false_for_value_out_of_range()
    {
        $this->assertFalse(
            $this->makeComment()->settings()->isValid('range', 6)
        );
    }

    /**
     * @test
     */
    public function range_rule_handles_negative_numbers()
    {
        $comment = $this->makeComment();

        $this->assertTrue(
            $comment->settings()->isValid('range2', -6)
        );

        $this->assertFalse(
            $comment->settings()->isValid('range2', -16)
        );
    }
}
