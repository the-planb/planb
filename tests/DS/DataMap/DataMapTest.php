<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\DataMap;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PlanB\DS\DataMap\DataList;
use PlanB\DS\DataMap\DataMap;
use PlanB\DS\DataMap\Exception\InvalidElementType;


final class DataMapTest extends TestCase
{
    public function test_it_is_instantiable()
    {
        $map = $this->collect();
        $this->assertInstanceOf(DataMap::class, $map);
    }

    public function collect(iterable $input = [], string ...$types): DataMap
    {
        return DataMap::collect($input, ...$types);
    }

    public function test_it_contains_zero_elements_on_creation()
    {
        $map = $this->collect();
        $this->assertSame(0, $map->count());
        $this->assertSame(0, $map->length);
        $this->assertTrue($map->isEmpty());
        $this->assertFalse($map->isNotEmpty());
    }

    public function test_it_can_be_created_with_values()
    {
        $map = $this->collect([
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueB',
        ]);

        $this->assertSame(3, $map->count());
        $this->assertSame(3, $map->length);
        $this->assertFalse($map->isEmpty());
        $this->assertTrue($map->isNotEmpty());
    }

    public function test_it_recognizes_if_a_key_exists()
    {
        $map = $this->collect([
            'key' => 'value',
        ]);

        $this->assertTrue($map->hasKey('key'));
        $this->assertFalse($map->hasKey('XXX'));
    }

    public function test_it_recognizes_if_a_value_exists()
    {
        $map = $this->collect([
            'key' => 'value',
        ]);
        $this->assertTrue($map->hasValue('value'));
        $this->assertFalse($map->hasValue('XXX'));
    }

    public function test_it_can_get_an_element_by_key()
    {
        $map = $this->collect([
            'key' => 'value',
        ]);

        $this->assertSame('value', $map->get('key'));
    }

    public function test_get_method_returns_a_default_value_if_necessary()
    {
        $map = $this->collect();

        $this->assertSame('default', $map->get('key', 'default'));
    }

    public function test_get_method_throws_an_exception_when_key_not_exists()
    {
        $map = $this->collect();

        $this->expectException(OutOfBoundsException::class);
        $map->get('key');
    }

    public function test_it_can_remove_an_element_by_key()
    {
        $map = $this->collect([
            'key' => 'value',
        ]);

        $newMap = $map->remove('key');
        $this->assertTrue($newMap->isEmpty());
        $this->assertImmutable($map, $newMap);
    }

    public function assertImmutable(object $original, object $result)
    {
        $this->assertSame($original::class, $result::class);
        $this->assertNotEquals(spl_object_hash($original), spl_object_hash($result));
    }

    public function test_it_can_put_a_set_of_elements()
    {
        $map = $this->collect();

        $newMap = $map->putAll([
            'keyA' => 'valueA',
            'keyB' => 'valueB',
        ]);

        $this->assertTrue($newMap->hasKey('keyA'));
        $this->assertTrue($newMap->hasKey('keyB'));
        $this->assertTrue($newMap->isNotEmpty());

        $this->assertImmutable($map, $newMap);
    }

    public function test_it_can_put_a_single_element()
    {
        $map = $this->collect();

        $newMap = $map->put('keyA', 'valueB');
        $newMap = $newMap->put('keyB', 'valueB');

        $this->assertTrue($newMap->hasKey('keyA'));
        $this->assertTrue($newMap->hasKey('keyB'));

        $this->assertImmutable($map, $newMap);
    }

    public function test_it_only_allows_values_of_the_same_type_on_creation()
    {
        $this->expectException(InvalidElementType::class);
        $this->collect([
            'a' => 'value',
            'b' => 4,
        ]);
    }

    public function test_put_method_respects_the_map_type()
    {
        $map = $this->collect([
            'a' => 'value',
        ]);

        $this->assertSame(['string'], $map->types());

        $this->expectException(InvalidElementType::class);
        $map->put('b', 78);
    }

    public function test_putAll_method_respects_the_map_type()
    {
        $map = $this->collect([
            'a' => 'value',
        ]);

        $this->assertSame(['string'], $map->types());

        $this->expectException(InvalidElementType::class);
        $map->putAll([
            'b' => 89,
        ]);
    }

    public function test_it_can_be_converted_in_an_array()
    {
        $map = $this->collect([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ]);

        $expected = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $this->assertEquals($expected, $map->toArray());
    }

    public function test_it_returns_a_list_with_the_values_only()
    {
        $map = $this->collect([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ]);

        $values = $map->values();

        $this->assertInstanceOf(DataList::class, $values);
        $this->assertEquals([
            'A',
            'B',
            'C',
        ], $values->toArray());
    }

    public function test_it_returns_a_list_with_the_keys_only()
    {
        $map = $this->collect([
            'a' => 10,
            'b' => 20,
            'c' => 30,
        ]);

        $values = $map->keys();

        $this->assertInstanceOf(DataList::class, $values);
        $this->assertEquals([
            'a',
            'b',
            'c',
        ], $values->toArray());
    }

    /**
     * @dataProvider mapProvider
     */
    public function test_map_method_can_map_on_every_element($total, $expected)
    {
        $input  = (0 === $total) ? [] : range(1, $total);
        $map    = DataMap::collect($input);
        $mapped = $map->map(fn() => '*');

        $this->assertSame($expected, $mapped->toArray());
        $this->assertImmutable($map, $mapped);
        $this->assertInstanceOf(DataMap::class, $mapped);
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
        $map = DataMap::mixed(['a' => null, 'b' => 0, 'c' => 1, 'd' => '',]);

        $filtered = $map->filter();
        $this->assertSame(['c' => 1], $filtered->toArray());
    }

    public function test_it_filter_by_value()
    {
        $map = DataMap::collect(range(1, 10));

        $filtered = $map->filter(fn(int $number) => $number < 5);
        $this->assertSame([1, 2, 3, 4], $filtered->toArray());
    }


    public function test_it_filter_by_key()
    {
        $map = DataMap::collect(range('a', 'j'));

        $filtered = $map->filter(fn(string $_, $key) => $key < 5);

        $this->assertSame(['a', 'b', 'c', 'd', 'e'], $filtered->toArray());
    }

    public function test_it_can_be_sorted_by_key()
    {
        $response = $this->collect([
            'c' => 'C',
            'b' => 'B',
            'a' => 'A',
        ])->ksort();

        $this->assertSame([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ], $response->toArray());
    }

    public function test_it_can_be_sorted_by_key_with_a_custom_comparator()
    {
        $list = $this->collect([
            'españa'  => 'madrid',
            'francia' => 'paris',
            'uk'      => 'london',
        ]);

        $byLength = static fn($first, $second) => strlen($first) <=> strlen($second);

        $this->assertSame([
            'uk'      => 'london',
            'españa'  => 'madrid',
            'francia' => 'paris',

        ], $list->ksort($byLength)->toArray());
    }

    public function test_it_can_be_merged_with_other_values()
    {
        $list = $this->collect([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ]);

        $merged = $list->merge([
            'b' => 'B1',
            'd' => 'D',
        ]);

        $this->assertSame([
            'a' => 'A',
            'b' => 'B1',
            'c' => 'C',
            'd' => 'D',
        ], $merged->toArray());

        $this->assertImmutable($list, $merged);
    }

    public function test_it_returns_the_diff_with_other_values()
    {
        $one = $this->collect([
            'a' => 'A',
            'b' => 'B',
        ]);

        $two = $this->collect([
            'b' => 'B',
            'c' => 'C',
        ]);

        $this->assertSame(['a' => 'A'], $one->diff($two)->toArray());
        $this->assertSame(['c' => 'C'], $two->diff($one)->toArray());
    }

    public function test_it_returns_the_intersect_with_other_values()
    {
        $one = $this->collect([
            'a' => 'A',
            'b' => 'B',
        ]);

        $two = $this->collect([
            'b' => 'B',
            'c' => 'C',
        ]);

        $this->assertSame(['b' => 'B'], $one->intersect($two)->toArray());
        $this->assertSame(['b' => 'B'], $two->intersect($one)->toArray());
    }
}
