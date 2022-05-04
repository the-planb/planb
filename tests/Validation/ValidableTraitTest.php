<?php

declare(strict_types=1);

namespace PlanB\Tests\Validation;

use PHPUnit\Framework\TestCase;
use PlanB\Validation\Traits\Exception\ConstraintNotFoundException;
use PlanB\Validation\Traits\ValidableTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;

final class ValidableTraitTest extends TestCase
{
    public function test_it_can_auto_discover_the_constraint_class()
    {
        $sut = new Auto();
        $sut->assert('value');
        $this->addToAssertionCount(1);
    }

    public function test_it_throws_an_exception_when_cannt_auto_discover_the_constraint_class()
    {
        $sut = new NoAuto();

        $this->expectException(ConstraintNotFoundException::class);
        $this->expectExceptionMessage(
            "Class 'PlanB\Tests\Validation\NoAuto' hasn't an associated Constraint: 'PlanB\Tests\Validation\Constraint\NoAuto"
        );

        $sut->assert('value');
    }

    /**
     * @dataProvider validProvider
     */
    public function test_assert_method_return_nothing_when_call_it_with_a_valid_input($sut, $input)
    {
        is_array($input) ? $sut->assert(...$input) : $sut->assert($input);
        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider validProvider
     */
    public function test_isValid_method_return_true_when_call_it_with_a_valid_input($sut, $input)
    {
        $response = is_array($input) ? $sut->isValid(...$input) : $sut->isValid($input);
        $this->assertTrue($response);
    }

    public function validProvider()
    {
        return [
            [new Simple(), 'antonio'],
            [new Composed(), ['name' => 'antonio', 'lastName' => 'rodriguez']],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function test_isValid_method_return_false_when_call_it_with_a_valid_input($sut, $input)
    {
        $response = is_array($input) ? $sut->isValid(...$input) : $sut->isValid($input);
        $this->assertFalse($response);
    }

    /**
     * @dataProvider invalidProvider
     */
    public function test_assert_method_throws_an_exception_when_call_it_with_a_invalid_input($sut, $input)
    {
        $this->expectExceptionMessage('This value is too short. It should have 5 characters or more.');

        is_array($input) ? $sut->assert(...$input) : $sut->assert($input);
    }

    public function invalidProvider()
    {
        return [
            [new Simple(), 'pepe'],
            [new Composed(), ['name' => 'pepe', 'lastName' => 'rodriguez']],
        ];
    }

}

class Auto
{
    use ValidableTrait;
}

class NoAuto
{
    use ValidableTrait;
}

class Simple
{
    use ValidableTrait;

    public static function getConstraints(): Constraint|array
    {
        return new Length(['min' => 5]);
    }
}


class Composed
{
    use ValidableTrait;

    public static function getConstraints(): Constraint|array
    {
        return new Collection([
            'name'     => new Length(['min' => 5]),
            'lastName' => new Length(['min' => 5]),
        ]);
    }
}
