<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\DataMap;

use PHPUnit\Framework\TestCase;
use PlanB\DS\DataMap\DataList;
use PlanB\DS\DataMap\DataMap;
use PlanB\DS\DataMap\Exception\InvalidElementType;

final class SequenceTest extends TestCase
{
    public function test_it_contains_zero_elements_on_creation()
    {
        $sequence = $this->collect();
        $this->assertSame(0, $sequence->count());
        $this->assertSame(0, $sequence->length);
        $this->assertTrue($sequence->isEmpty());
        $this->assertFalse($sequence->isNotEmpty());
    }

    private function collect(iterable $input = [], string...$types)
    {
        return DataMap::collect($input, ...$types);
    }

    public function test_it_can_be_created_without_values()
    {
        $sequence = DataMap::empty();
        $this->assertTrue($sequence->isEmpty());
    }

    public function test_it_can_be_created_with_values()
    {
        $sequence = $this->collect([
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueB',
        ]);

        $this->assertSame(3, $sequence->count());
        $this->assertSame(3, $sequence->length);
        $this->assertFalse($sequence->isEmpty());
        $this->assertTrue($sequence->isNotEmpty());
    }

    public function test_types_can_be_forced_on_creation()
    {
        $this->expectException(InvalidElementType::class);
        $this->collect([
            'a' => 'value',
        ], 'int');
    }


    public function test_it_knows_when_any_element_meets_a_condition()
    {
        $sequence = $this->collect([
            'key' => 'value',
        ]);

        $conditionA = fn(string $value, string $key) => $key === 'key';
        $conditionB = fn(string $value, string $key) => $key === 'xxx';

        $this->assertTrue($sequence->any($conditionA));
        $this->assertFalse($sequence->any($conditionB));
    }

    public function test_it_knows_when_all_elements_meet_a_condition()
    {
        $sequence = $this->collect([
            'a' => 'value',
            'b' => 'value',
        ]);

        $conditionA = fn(string $value) => $value === 'value';
        $conditionB = fn(string $value, string $key) => $key === 'a';

        $this->assertTrue($sequence->all($conditionA));
        $this->assertFalse($sequence->all($conditionB));
    }

    /**
     * @dataProvider eachProvider
     */
    public function test_it_iterates_once_by_element(int $total)
    {
        $log   = 0;
        $input = 0 === $total ? [] : range(1, $total);

        $sequence = $this->collect($input);
        $sequence->each(function () use (&$log) {
            $log++;
        });

        $this->assertSame($total, $log);
    }

    public function eachProvider(): array
    {
        return [
            [0],
            [1],
            [2],
        ];
    }

    public function test_it_can_reduce_the_elements_in_a_single_value()
    {
        $response = DataList::range('a', 'c')
            ->reduce(function (string $carry, string $item, int $key) {
                return $carry.$key.$item;
            }, '');

        $this->assertSame('0a1b2c', $response);
    }

    public function test_it_can_be_sorted_by_value()
    {
        $list   = DataList::range('c', 'a');
        $sorted = $list->sort();

        $this->assertImmutable($list, $sorted);
        $this->assertSame(['a', 'b', 'c'], $sorted->toArray());
    }

    public function assertImmutable(object $original, object $result)
    {
        $this->assertSame($original::class, $result::class);
        $this->assertNotEquals(spl_object_hash($original), spl_object_hash($result));
    }

    public function test_it_can_be_sorted_by_value_with_a_custom_comparator()
    {
        $list = DataList::collect([
            [1, 'antonio', 24],
            [2, 'maria', 36],
            [3, 'andres', 48],
            [4, 'ana', 30],
        ]);

        $byName = static fn($first, $second) => $first[1] <=> $second[1];

        $this->assertSame([
            [4, 'ana', 30],
            [3, 'andres', 48],
            [1, 'antonio', 24],
            [2, 'maria', 36],
        ], $list->sort($byName)->toArray());

        $byAge = static fn($first, $second) => $first[2] <=> $second[2];

        $this->assertSame([
            [1, 'antonio', 24],
            [4, 'ana', 30],
            [2, 'maria', 36],
            [3, 'andres', 48],
        ], $list->sort($byAge)->toArray());
    }
}
