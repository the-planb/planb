<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\DataMap;

use OutOfRangeException;
use PHPUnit\Framework\TestCase;
use PlanB\DS\DataMap\DataList;
use PlanB\DS\DataMap\Exception\InvalidElementType;

final class DataListTest extends TestCase
{
    public function test_it_is_instantiable()
    {
        $list = $this->collect();
        $this->assertInstanceOf(DataList::class, $list);
    }

    public function collect(iterable $input = [], string ...$types)
    {
        return DataList::collect($input, ...$types);
    }

    public function test_it_contains_zero_elements_on_creation()
    {
        $list = $this->collect();
        $this->assertSame(0, $list->count());
        $this->assertSame(0, $list->length);
        $this->assertTrue($list->isEmpty());
        $this->assertFalse($list->isNotEmpty());
    }

    public function test_it_can_be_created_with_values()
    {
        $list = $this->collect([
            'valueA',
            'valueB',
            'valueB',
        ]);

        $this->assertSame(3, $list->count());
        $this->assertSame(3, $list->length);
        $this->assertFalse($list->isEmpty());
        $this->assertTrue($list->isNotEmpty());
    }

    public function test_it_recognizes_if_a_index_exists()
    {
        $list = $this->collect([
            'value',
        ]);

        $this->assertTrue($list->hasIndex(0));
        $this->assertFalse($list->hasIndex(1));
    }

    public function test_it_recognizes_if_a_value_exists()
    {
        $list = $this->collect([
            'value',
        ]);
        $this->assertTrue($list->hasValue('value'));
        $this->assertFalse($list->hasValue('XXX'));
    }

    public function test_it_recognizes_if_contains_a_set_of_values()
    {
        $list = $this->collect([
            'first',
            'second',
        ]);
        $this->assertTrue($list->contains('first', 'second'));
        $this->assertFalse($list->contains('first', 'second', 'third'));
    }


    public function test_it_can_get_an_element_by_index()
    {
        $list = $this->collect([
            'value',
        ]);

        $this->assertSame('value', $list->get(0));
    }

    public function test_get_method_returns_a_default_value_if_necessary()
    {
        $list = $this->collect();

        $this->assertSame('default', $list->get(0, 'default'));
    }

    public function test_get_method_throws_an_exception_when_key_not_exists()
    {
        $list = $this->collect();

        $this->expectException(OutOfRangeException::class);
        $list->get(0);
    }

    public function test_it_can_remove_an_element_by_key()
    {
        $list = $this->collect([
            'value',
        ]);

        $newList = $list->remove(0);
        $this->assertTrue($newList->isEmpty());
        $this->assertImmutable($list, $newList);
    }

    public function assertImmutable(object $original, object $result)
    {
        $this->assertSame($original::class, $result::class);
        $this->assertNotEquals(spl_object_hash($original), spl_object_hash($result));
    }

    public function test_it_can_add_a_set_of_elements()
    {
        $list = $this->collect();

        $newList = $list->addAll([
            'a' => 'valueA',
            'b' => 'valueB',
        ]);

//        dump($newList);

        $this->assertTrue($newList->hasValue('valueA'));
        $this->assertTrue($newList->hasValue('valueB'));
        $this->assertTrue($newList->isNotEmpty());

        $this->assertImmutable($list, $newList);
    }

    public function test_it_can_add_a_single_element()
    {
        $list = $this->collect();

        $newList = $list->add('valueA');
        $newList = $newList->add('valueB');

        $this->assertTrue($newList->hasValue('valueA'));
        $this->assertTrue($newList->hasValue('valueB'));

        $this->assertImmutable($list, $newList);
    }

    public function test_it_only_allows_values_of_the_same_type_on_creation()
    {
        $this->expectException(InvalidElementType::class);
        $this->collect([
            'value',
            4,
        ]);
    }

    public function test_add_method_respects_the_list_type()
    {
        $list = $this->collect([
            'value',
        ]);

        $this->assertSame(['string'], $list->types());

        $this->expectException(InvalidElementType::class);
        $list->add(78);
    }

    public function addAll_method_respects_the_list_type()
    {
        $list = $this->collect([
            'value',
        ]);

        $this->assertSame(['string'], $list->types());

        $this->expectException(InvalidElementType::class);
        $list->addAll([
            89,
        ]);
    }

    public function test_it_can_be_converted_in_an_array()
    {
        $map = $this->collect([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ]);

        $this->assertEquals([
            'A',
            'B',
            'C',
        ], $map->toArray());
    }


    public function test_it_can_be_created_with_from_range()
    {
        $sequence = DataList::range(1, 10);
        $this->assertSame(10, $sequence->length);
    }

    public function test_it_can_be_created_with_from_range_and_a_step()
    {
        $sequence = DataList::range(1, 2, .1);
        $this->assertSame(11, $sequence->length);
    }

    public function test_it_can_be_created_with_from_range_of_letters()
    {
        $sequence = DataList::range('a', 'z');
        $this->assertSame(26, $sequence->length);
    }

    /**
     * @dataProvider mapProvider
     */
    public function test_map_method_can_map_on_every_element($total, $expected)
    {
        $map = DataList::empty();
        if (0 !== $total) {
            $map = DataList::range(1, $total);
        }

        $mapped = $map->map(fn() => '*');

        $this->assertSame($expected, $mapped->toArray());
        $this->assertImmutable($map, $mapped);
        $this->assertInstanceOf(DataList::class, $mapped);
    }

    public function mapProvider()
    {
        return [
            [0, []],
            [1, ['*']],
            [2, ['*', '*']],
        ];
    }

    public function test_it_filter_the_empty_values()
    {
        $map = DataList::mixed([null, 0, 1, '',]);

        $filtered = $map->filter();
        $this->assertSame([1], $filtered->toArray());
    }

    public function test_it_filter_by_value()
    {
        $map = DataList::range(1, 10);

        $filtered = $map->filter(fn(int $number) => $number < 5);
        $this->assertSame([1, 2, 3, 4], $filtered->toArray());
    }


    public function test_it_filter_by_key()
    {
        $map = DataList::range('a', 'j');

        $filtered = $map->filter(fn(string $_, $key) => $key < 5);

        $this->assertSame(['a', 'b', 'c', 'd', 'e'], $filtered->toArray());
    }

    public function test_it_can_be_merged_with_other_values()
    {
        $list   = DataList::range(1, 5);
        $merged = $list->merge([6, 7, 8]);

        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8], $merged->toArray());
        $this->assertImmutable($list, $merged);
    }
}
