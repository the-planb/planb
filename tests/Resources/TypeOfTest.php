<?php

declare(strict_types=1);

namespace PlanB\Tests\Resources\functions;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TypeOfTest extends TestCase
{
    #[DataProvider('typeOfProvider')]
    public function test_type_of_function_returns_the_right_type(mixed $val, string $expected)
    {
        $this->assertSame($expected, type_of($val));
    }

    public static function typeOfProvider(): array
    {
        $resource = opendir(__DIR__);
        closedir($resource);

        return [
            [new stdClass(), stdClass::class],
            [fn () => true, 'callable'],
            [opendir(__DIR__), 'resource'],
            [$resource, 'resource'],
            [null, 'null'],
            [1, 'integer'],
            [1.0, 'double'],
            ['string', 'string'],
            [[], 'array'],
        ];
    }

    #[DataProvider('isOfTheTypeProvider')]
    public function test_is_of_the_type_function_returns_false_if_no_types_are_provided(mixed $val)
    {
        $this->assertTrue(is_of_the_type($val));
    }

    #[DataProvider('isOfTheTypeProvider')]
    public function test_is_of_the_type_function_returns_true_on_right_types(mixed $val, $type)
    {
        $this->assertTrue(is_of_the_type($val, $type));
    }

    #[DataProvider('isOfTheTypeProvider')]
    public function test_is_of_the_type_function_returns_true_when_type_is_mixed(mixed $val)
    {
        $this->assertTrue(is_of_the_type($val, 'mixed'));
    }

    public static function isOfTheTypeProvider(): array
    {
        return [
            [new stdClass(), stdClass::class],
            [new stdClass(), 'object'],
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
