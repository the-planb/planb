<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Attribute\ElementType;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Map\Map;
use PlanB\DS\Map\MapImmutableInterface;
use PlanB\DS\Map\MapInterface;
use PlanB\Tests\DS\Traits\Assertions;
use PlanB\Tests\DS\Traits\ObjectMother;

final class MapTest extends TestCase
{
    use ObjectMother;
    use Assertions;

    //CORE
    public function test_it_can_be_instantiate()
    {
        $map = Map::collect();
        $this->assertInstanceOf(Map::class, $map);
        $this->assertInstanceOf(CollectionInterface::class, $map);
        $this->assertInstanceOf(MapInterface::class, $map);
    }

    public function test_it_can_be_instantiate_using_a_mapping_function()
    {
        $vector = Map::collect([1, 2, 3], fn(int $i) => str_repeat('*', $i));
        $this->assertEquals([
            '*',
            '**',
            '***',
        ], $vector->toArray());
    }

    //INFO
    public function test_it_knows_if_has_a_key()
    {
        $map = $this->give_me_a_map();
        $this->assertTrue($map->hasKey('A'));

        $this->assertFalse($map->hasKey('F'));
    }

    public function test_it_gets_a_vector_with_the_values()
    {
        $map = $this->give_me_a_map();
        $expected = $this->give_me_a_vector([
            'value/A',
            'value/B',
            'value/C',
            'value/D',
        ]);

        $this->assertEquals($expected, $map->values());
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_gets_a_vector_with_the_keys()
    {
        $map = $this->give_me_a_map();
        $expected = $this->give_me_a_vector([
            'A',
            'B',
            'C',
            'D',
        ]);

        $this->assertEquals($expected, $map->keys());
        $this->assertCollectionHasNotChange($map);
    }

    //MODIFICATION
    public function test_it_normalize_keys_properly()
    {
        $input = ['beatriz', 'maria', 'ana'];
        $expected = ['BEATRIZ' => 'beatriz', 'MARIA' => 'maria', 'ANA' => 'ana'];

        $class = new #[ElementType('string')] class extends Map {
            public function normalizeKey(mixed $value, string|int $key): string|int
            {
                return strtoupper($value);
            }
        };

        $map = $class::collect($input);

        $this->assertSame($expected, $map->toArray());
    }

    public function test_it_can_sort_a_collection_using_natural_order_with_keys()
    {
        $input = ['beatriz' => 0, 'maria' => 10, 'luis' => 50, 'ana' => 20];
        $expected = ['ana' => 20, 'beatriz' => 0, 'luis' => 50, 'maria' => 10];


        $collection = $this->give_me_a_map($input);
        $sorted = $collection->ksort();

        $this->assertSame($expected, $sorted->toArray());
        $this->assertSame($input, $collection->toArray());
    }

    public function test_it_can_sort_a_collection_using_a_custom_order_with_keys()
    {
        $input = ['beatriz' => 0, 'maria' => 10, 'luis' => 50, 'ana' => 20];
        $expected = ['maria' => 10, 'ana' => 20, 'luis' => 50, 'beatriz' => 0];

        $collection = $this->give_me_a_map($input);

        //compara el reverso de las cadenas
        $comparison = fn(string $first, string $second) => strrev($first) <=> strrev($second);

        $sorted = $collection->ksort($comparison);

        $this->assertSame($expected, $sorted->toArray());
        $this->assertSame($input, $collection->toArray());
    }

    public function test_it_can_be_mapped()
    {
        $map = $this->give_me_a_map();
        $expected = $this->give_me_a_map([
            'A' => 'VALUE/A',
            'B' => 'VALUE/B',
            'C' => 'VALUE/C',
            'D' => 'VALUE/D',
        ]);

        $callback = fn(string $value) => strtoupper($value);
        $mapped = $map->map($callback);

        $this->assertEquals($expected, $mapped);
        $this->assertInstanceOf(MapImmutableInterface::class, $mapped);

        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_be_mapped_byKey()
    {
        $map = $this->give_me_a_map();
        $expected = $this->give_me_a_map([
            'a' => 'value/A',
            'b' => 'value/B',
            'c' => 'value/C',
            'd' => 'value/D',
        ]);

        $callback = fn(string $value, string $key) => strtolower($key);
        $mapped = $map->mapKeys($callback);

        $this->assertEquals($expected, $mapped);
        $this->assertInstanceOf(MapImmutableInterface::class, $mapped);

        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_map_the_keys_by_ref()
    {
        $map = $this->give_me_a_map();
        $expected = $this->give_me_a_map([
            'a' => 'VALUE/A',
            'b' => 'VALUE/B',
            'c' => 'VALUE/C',
            'd' => 'VALUE/D',
        ]);

        $callback = function (string $value, string &$key) {
            $key = strtolower($key);

            return strtoupper($value);
        };

        $mapped = $map->map($callback);

        $this->assertEquals($expected, $mapped);
        $this->assertInstanceOf(MapImmutableInterface::class, $mapped);

        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_be_merged()
    {
        $map = $this->give_me_a_map();
        $expected = $this->give_me_a_map([
            'A' => 'value/A',
            'B' => 'value/X',
            'C' => 'value/Y',
            'D' => 'value/D',
        ]);

        $input = $this->give_me_a_map([
            'B' => 'value/X',
            'C' => 'value/Y',
        ]);

        $this->assertEquals($expected, $map->merge($input));
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_get_the_difference_comparing_by_keys()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $expected = $this->give_me_a_map([
            'A' => 'value/A',
            'D' => 'value/D',
        ]);

        $this->assertEquals($expected, $map->diffKeys($input));
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_get_the_difference_comparing_by_keys_with_a_custom_comparison()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'b' => 'value/B',
            'c' => 'value/C',
        ]);

        $expected = $this->give_me_a_map([
            'A' => 'value/A',
            'D' => 'value/D',
        ]);

        $comparison = strcasecmp(...);

        $this->assertEquals($expected, $map->diffKeys($input, $comparison));
    }

    public function test_it_can_get_the_intersection_comparing_by_values()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $expected = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $this->assertEquals($expected, $map->intersect($input));
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_get_the_intersection_comparing_by_values_with_a_custom_comparison()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'b' => 'value/B',
            'c' => 'value/C',
        ]);

        $expected = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $comparison = strcasecmp(...);

        $this->assertEquals($expected, $map->intersect($input, $comparison));
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_get_the_intersection_comparing_by_keys()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $expected = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $this->assertEquals($expected, $map->intersectKeys($input));
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_get_the_intersection_comparing_by_keys_with_a_custom_comparison()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'b' => 'value/B',
            'c' => 'value/C',
        ]);

        $expected = $this->give_me_a_map([
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $comparison = strcasecmp(...);

        $this->assertEquals($expected, $map->intersectKeys($input, $comparison));
    }

}
