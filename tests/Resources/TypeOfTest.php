<?php

declare(strict_types=1);

namespace PlanB\Tests\Resources\functions;

use PHPUnit\Framework\TestCase;

final class TypeOfTest extends TestCase
{

    private $resource;


    /**
     * @dataProvider typeOfProvider
     */
    public function test_type_of_function_returns_the_right_type(mixed $val, string $expected)
    {
        $this->assertSame($expected, type_of($val));
    }

    public function typeOfProvider()
    {
        $resource = opendir(__DIR__);
        closedir($resource);

        return [
            [$this, $this::class],
            [fn() => true, 'callable'],
            [opendir(__DIR__), 'resource'],
            [$resource, 'resource'],
            [null, 'null'],
            [1, 'integer'],
            [1.0, 'double'],
            ['string', 'string'],
            [[], 'array'],
        ];
    }

    /**
     * @dataProvider isOfTheTypeProvider
     */
    public function test_is_of_the_type_function_returns_false_if_no_types_are_provided(mixed $val)
    {
        $this->assertFalse(is_of_the_type($val));
    }

    /**
     * @dataProvider isOfTheTypeProvider
     */
    public function test_is_of_the_type_function_returns_true_on_right_types(mixed $val, $type)
    {
        $this->assertTrue(is_of_the_type($val, $type));
    }

    /**
     * @dataProvider isOfTheTypeProvider
     */
    public function test_is_of_the_type_function_returns_true_when_type_is_mixed(mixed $val)
    {
        $this->assertTrue(is_of_the_type($val, 'mixed'));
    }

    public function isOfTheTypeProvider()
    {
        return [
            [$this, $this::class],
            [$this, 'object'],
            [opendir(__DIR__), 'resource'],
            [['array'], 'array'],
            ['string', 'string'],
            [1, 'int'],
            [1, 'integer'],
            [1.0, 'float'],
            [1.0, 'double'],
            [true, 'bool'],
            [false, 'boolean'],
            [null, 'null'],
            [strtoupper(...), 'callable'],
            [['array'], 'countable'],
            [['array'], 'iterable'],
        ];
    }

}
