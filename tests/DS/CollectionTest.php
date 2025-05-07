<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use Countable;
use DateTime;
use Exception;
use IteratorAggregate;
use JsonSerializable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Exception\ElementNotFoundException;
use PlanB\DS\Exception\InvalidElementType;
use PlanB\DS\Vector\Vector;
use PlanB\Tests\DS\Traits\Assertions;
use PlanB\Tests\DS\Traits\ObjectMother;
use PlanB\Type\ArrayValue;
use stdClass;

final class CollectionTest extends TestCase
{
    use ObjectMother;

    use Assertions;

    //CORE
    public function test_a_collection_implements_the_right_interfaces()
    {
        $collection = $this->give_me_an_empty_collection();

        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertInstanceOf(Countable::class, $collection);
        $this->assertInstanceOf(JsonSerializable::class, $collection);
        $this->assertInstanceOf(IteratorAggregate::class, $collection);
        $this->assertInstanceOf(ArrayValue::class, $collection);
    }

    public function test_it_extracts_the_types_from_the_elementType_attribute_properly()
    {
        $collection = $this->give_me_a_typed_collection();

        $this->assertEquals([
            'string',
            DateTime::class,

        ], $collection->getAllowedTypes());
    }

    public function test_it_merge_types_from_the_elementType_attribute_and_constructor_properly()
    {
        $collection = $this->give_me_a_typed_collection(types: ['int']);

        $this->assertEquals([
            'string',
            DateTime::class,
            'int',

        ], $collection->getAllowedTypes());
    }

    public function test_it_discard_the_nulls_values()
    {
        $collection = $this->give_me_a_collection([
            null, 'a', 'b', null, 'c',
        ], ['string']);

        $this->assertEquals(['a', 'b', 'c'], array_values($collection->toArray()));
    }

    public function test_it_allows_nulls_values_when_null_type_is_present()
    {
        $collection = $this->give_me_a_collection([
            null, 'a', 'b', null, 'c',
        ], ['string', 'null']);

        $this->assertEquals([null, 'a', 'b', null, 'c'], array_values($collection->toArray()));
    }

    public function test_it_remove_null_values_by_default()
    {
        $collection = $this->give_me_a_collection([
            null, 'a', 'b', null, 'c',
        ], ['string']);

        $this->assertEquals(['a', 'b', 'c'], array_values($collection->toArray()));
    }

    //CORE
    public function test_it_throws_an_exception_when_is_instantiated_with_an_invalid_element()
    {
        $this->expectException(InvalidElementType::class);
        $this->expectExceptionMessage("The element is of type 'stdClass' but only [string, DateTime] are allowed");

        $this->give_me_a_typed_collection([
            new stdClass(),
        ]);
    }

    public function test_it_is_instantiable_by_a_static_method()
    {
        $collection = Vector::collect([
            'value/A',
            'value/B',
        ]);

        $this->assertInstanceOf(CollectionInterface::class, $collection);
    }

    public function test_it_returns_null_when_fails_on_creation()
    {
        $collection = Vector::tryFrom([1, 2, 3]);

        $this->assertNotNull($collection);

        $collection = Vector::tryFrom([1, 2, 3], function () {
            throw new Exception();
        });

        $this->assertNull($collection);
    }

    public function test_it_is_normalizable()
    {
        $collection = Vector::collect([
            'a',
            'b',
        ]);

        $collection = $collection->normalize(strtoupper(...), ord(...));

        $this->assertEquals([65, 66], $collection->toArray());
    }

    public function test_it_is_instantiable_using_a_traversable_like_input()
    {
        $data = $this->give_me_an_array();
        $input = $this->give_me_a_collection($data);
        $collection = $this->give_me_a_collection($input);

        $this->assertEquals($data, $collection->toArray());
        $this->assertInstanceOf(CollectionInterface::class, $collection);
    }

    public function test_it_is_instantiable_using_the_cartesian_product_of_some_arrays()
    {
        $collection = Vector::fromCartesian(function (int $a, int $b, int $c) {
            return $a + $b + $c;
        }, [1, 2], [10, 20], \vector([100, 200]));

        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertEquals([111, 211, 121, 221, 112, 212, 122, 222], $collection->toArray());
    }

    public function test_it_can_be_used_in_a_loop()
    {
        $data = [];
        $input = $this->give_me_an_array();

        $collection = $this->give_me_a_collection($input);
        foreach ($collection as $key => $value) {
            $data[$key] = $value;
        }

        $this->assertEquals($input, $data);
    }


    #[DataProvider('countProvider')]
    public function test_it_knows_how_many_elements_has(array $input, int $total, $empty)
    {
        $collection = $this->give_me_a_collection($input);

        $this->assertSame($total, $collection->count());
        $this->assertSame($empty, $collection->isEmpty());
        $this->assertSame(!$empty, $collection->isNotEmpty());
    }

    //COUNT

    public static function countProvider(): array
    {
        $data = fn (int $total) => array_fill(0, $total, '*');

        return [
            [$data(0), 0, true],
            [$data(1), 1, false],
            [$data(2), 2, false],
        ];
    }

    public function test_it_can_get_a_value_by_key()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame('value/B', $collection->get('B'));
    }

    //GETTERS

    public function test_it_can_get_a_default_value_by_key()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame('value/X', $collection->get('X', 'value/X'));
    }

    public function test_it_throws_an_exception_when_try_to_get_an_unkown_key_without_default_value()
    {
        $collection = $this->give_me_a_collection();

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage("The key 'X' doesn't exist");
        $this->assertSame('value/X', $collection->get('X'));
    }

    public function test_it_gets_the_first_element()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame('value/A', $collection->first());

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_gets_the_first_element_that_meets_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn (string $value) => str_ends_with($value, 'B');
        $this->assertSame('value/B', $collection->firstThat($condition));

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_firstThat_method_returns_null_when_no_element_meets_the_condition()
    {
        $collection = $this->give_me_a_collection();
        $condition = fn (string $value) => str_ends_with($value, 'X');
        $founded = $collection->firstThat($condition);
        $this->assertNull($founded);
    }

    public function test_firstThat_method_returns_null_when_collection_is_empty()
    {
        $collection = $this->give_me_a_collection([]);
        $condition = fn (string $value) => str_ends_with($value, 'X');
        $founded = $collection->firstThat($condition);
        $this->assertNull($founded);
    }

    public function test_it_gets_the_last_element()
    {
        $collection = $this->give_me_a_collection();
        $this->assertSame('value/D', $collection->last());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_last_method_returns_null_when_collection_is_empty()
    {
        $collection = $this->give_me_an_empty_collection();

        $founded = $collection->last();
        $this->assertNull($founded);
    }

    public function test_it_gets_the_last_element_that_meets_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn (string $value) => str_starts_with($value, 'value');
        $this->assertSame('value/D', $collection->lastThat($condition));

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_lastThat_method_returns_null_when_no_element_meets_the_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn (string $value) => str_ends_with($value, 'X');
        $founded = $collection->lastThat($condition);
        $this->assertNull($founded);
    }

    public function test_lastThat_method_returns_null_when_collection_is_empty()
    {
        $collection = $this->give_me_a_collection([]);

        $condition = fn (string $value) => str_ends_with($value, 'X');
        $founded = $collection->lastThat($condition);
        $this->assertNull($founded);
    }

    public function test_it_gets_the_init_of_the_collection()
    {
        $collection = $this->give_me_a_collection();

        $expected = $this->give_me_a_collection([
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $this->assertEquals($expected, $collection->init());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_the_init_of_an_empty_collection_is_an_empty_collection()
    {
        $collection = $this->give_me_an_empty_collection();
        $expected = $this->give_me_an_empty_collection();

        $this->assertEquals($expected, $collection->init());
        $this->assertSame([], $collection->toArray());
    }

    public function test_it_gets_the_tail_of_the_collection()
    {
        $collection = $this->give_me_a_collection();

        $expected = $this->give_me_a_collection([
            'B' => 'value/B',
            'C' => 'value/C',
            'D' => 'value/D',
        ]);

        $this->assertEquals($expected, $collection->tail());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_the_tail_of_an_empty_collection_is_an_empty_collection()
    {
        $collection = $this->give_me_an_empty_collection();
        $expected = $this->give_me_an_empty_collection();

        $this->assertEquals($expected, $collection->tail());
        $this->assertSame([], $collection->toArray());
    }

    public function test_it_can_take_the_n_first_elements_of_a_collection()
    {
        $collection = $this->give_me_a_collection();
        $expected = $this->give_me_a_collection([
            'A' => 'value/A',
            'B' => 'value/B',
        ]);

        $this->assertEquals($expected, $collection->take(2));

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_take_method_works_with_zero_or_negative_numbers()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals([], $collection->take(0)->toArray());
        $this->assertEquals(['D' => 'value/D'], $collection->take(-1)->toArray());
        $this->assertEquals(['C' => 'value/C', 'D' => 'value/D'], $collection->take(-2)->toArray());

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_take_method_returns_all_elemetns_if_param_is_greater_than_the_collection_size()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals($collection, $collection->take(10));
        $this->assertNotSame($collection, $collection->take(10));
    }

    public function test_it_can_drop_the_n_first_elements_of_a_collection()
    {
        $collection = $this->give_me_a_collection();
        $expected = $this->give_me_a_collection([
            'C' => 'value/C',
            'D' => 'value/D',
        ]);

        $this->assertEquals($expected, $collection->drop(2));

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_drop_method_works_with_zero_or_negative_numbers()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals($collection, $collection->drop(0));
        $this->assertNotSame($collection, $collection->drop(0));


        $this->assertEquals([
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
        ], $collection->drop(-1)->toArray());

        $this->assertEquals([
            'A' => 'value/A',
            'B' => 'value/B',
        ], $collection->drop(-2)->toArray());


        //        $this->assertNotSame($collection, $collection->drop(-1));
    }

    public function test_it_drop_method_returns_an_empty_collection_if_param_greater_than_collection_size()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals([], $collection->drop(10)->toArray());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_can_take_the_first_elements_that_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();
        $condition = fn (string $value) => $value !== 'value/C';
        $expected = $this->give_me_a_collection([
            'A' => 'value/A',
            'B' => 'value/B',
        ]);

        $this->assertEquals($expected, $collection->takeWhile($condition));
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_can_drop_the_first_elements_that_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();
        $condition = fn (string $value) => $value !== 'value/C';
        $expected = $this->give_me_a_collection([
            'C' => 'value/C',
            'D' => 'value/D',
        ]);

        $this->assertEquals($expected, $collection->dropWhile($condition));

        $this->assertCollectionHasNotChange($collection);
    }

    //INFO

    public function test_it_knows_if_has_a_value_or_not()
    {
        $collection = $this->give_me_a_collection();

        $this->assertTrue($collection->hasValue('value/A'));
        $this->assertFalse($collection->hasValue('value/X'));
    }

    public function test_it_knows_if_has_some_values_or_not()
    {
        $collection = $this->give_me_a_collection();

        $this->assertTrue($collection->contains('value/B', 'value/A'));
        $this->assertFalse($collection->contains('value/B', 'value/A', 'value/X'));
    }

    public function test_it_can_find_the_key_or_index_of_a_value()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame('A', $collection->find('value/A'));
        $this->assertSame('B', $collection->find('value/B'));
        $this->assertNull($collection->find('value/X'));
    }

    public function test_it_knows_if_some_element_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $conditionA = fn (string $value) => str_ends_with($value, '/A');
        $conditionB = fn (string $value) => str_ends_with($value, '/B');
        $conditionC = fn (string $value) => str_ends_with($value, '/X');

        $this->assertTrue($collection->some($conditionA));
        $this->assertTrue($collection->some($conditionB));
        $this->assertFalse($collection->some($conditionC));
    }

    public function test_it_knows_if_every_the_elements_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $conditionYes = fn (string $value) => str_starts_with($value, 'value/');
        $conditionNO = fn (string $value) => str_ends_with($value, '/A');

        $this->assertTrue($collection->every($conditionYes));
        $this->assertFalse($collection->every($conditionNO));
    }

    //MODIFICATION

    public function test_it_iterate_once_for_every_element()
    {
        $collection = $this->give_me_a_collection();
        $log = '';

        $collection->each(function (string $_, string $key) use (&$log) {
            $log .= $key;
        });

        $this->assertSame('ABCD', $log);
    }

    public function test_it_can_filter_the_empty_values()
    {
        $input = $expected = $this->give_me_an_array();
        $input['A2'] = '';
        $input['B2'] = null;
        $input['C2'] = 0;
        $input['D2'] = false;

        $collection = $this->give_me_a_collection($input);
        $filtered = $collection->filter();

        $this->assertSame($expected, $filtered->toArray());
    }

    public function test_it_can_filter_using_a_condition()
    {
        $input = $expected = $this->give_me_an_array();
        $input['A2'] = 'value/A/X';
        $input['B2'] = 'value/B/X';
        $input['C2'] = 'value/C/X';

        $condition = fn (string $_, string $key) => !str_ends_with($key, '2');

        $collection = $this->give_me_a_collection($input);
        $filtered = $collection->filter($condition);

        $this->assertSame($expected, $filtered->toArray());
        $this->assertSame($input, $collection->toArray());
    }

    public function test_it_can_sort_a_collection_using_natural_order_with_values()
    {
        $input = ['a' => 'maria', 'b' => 'luis', 'c' => 'ana', 'd' => 'beatriz'];
        $expected = ['c' => 'ana', 'd' => 'beatriz', 'b' => 'luis', 'a' => 'maria'];

        $collection = $this->give_me_a_collection($input);
        $sorted = $collection->sort();

        $this->assertSame($expected, $sorted->toArray());
        $this->assertSame($input, $collection->toArray());
    }

    public function test_it_can_sort_a_collection_using_a_custom_order_with_values()
    {
        $input = ['a' => 'beatriz', 'b' => 'maria', 'c' => 'luis', 'd' => 'ana'];
        $expected = ['b' => 'maria', 'd' => 'ana', 'c' => 'luis', 'a' => 'beatriz'];

        $collection = $this->give_me_a_collection($input);

        //compara el reverso de las cadenas
        $comparison = fn (string $first, string $second) => strrev($first) <=> strrev($second);

        $sorted = $collection->sort($comparison);

        $this->assertSame($expected, $sorted->toArray());
        $this->assertSame($input, $collection->toArray());
    }

    public function test_it_can_get_the_difference_comparing_by_values()
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

        $this->assertEquals($expected, $map->diff($input));
        $this->assertCollectionHasNotChange($map);
    }


    public function test_it_can_get_the_difference_comparing_by_values_with_a_custom_comparison()
    {
        $map = $this->give_me_a_map();

        $input = $this->give_me_a_map([
            'B' => 'VALUE/B',
            'C' => 'VALUE/C',
        ]);

        $expected = $this->give_me_a_map([
            'A' => 'value/A',
            'D' => 'value/D',
        ]);

        $comparison = strcasecmp(...);

        $this->assertEquals($expected, $map->diff($input, $comparison));
        $this->assertCollectionHasNotChange($map);
    }

    public function test_it_can_remove_duplicate_values()
    {
        $collection = $this->give_me_a_collection([
            'A' => 'value/A',
            'B' => 'value/B',
            'A1' => 'value/A',
            'C' => 'value/C',
            'B1' => 'value/B',
            'D' => 'value/D',
        ]);

        $expected = $this->give_me_a_collection([
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
            'D' => 'value/D',
        ]);

        $this->assertEquals($expected, $collection->unique());
        $this->assertNotSame($expected, $collection);
    }


    public function test_it_can_remove_duplicate_values_using_a_criteria()
    {
        $collection = $this->give_me_a_collection([
            'A' => 'AAAA',
            'B' => 'AAAAAA',
            'A1' => 'ABAB',
            'C' => 'AAAAAAAA',
            'B1' => 'ABABAB',
        ]);

        $expected = $this->give_me_a_collection([
            'A' => 'AAAA',
            'B' => 'AAAAAA',
            'C' => 'AAAAAAAA',
        ]);

        $this->assertEquals($expected, $collection->unique(fn (string $value) => strlen($value)));
        $this->assertNotSame($expected, $collection);
    }

    public function test_it_can_be_reversed()
    {
        $collection = $this->give_me_a_collection();
        $expected = $this->give_me_a_collection([
            'D' => 'value/D',
            'C' => 'value/C',
            'B' => 'value/B',
            'A' => 'value/A',
        ]);

        $this->assertEquals($expected, $collection->reversed());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_can_be_reduced_to_a_single_value()
    {
        $collection = $this->give_me_a_collection();

        $reduced = $collection->reduce(function (string $carry, string $value, string $key) {
            return $carry .= $key;
        }, '');

        $this->assertSame('ABCD', $reduced);
    }

    public function test_it_can_be_flattened()
    {
        $collection = $this->give_me_a_collection([
            ['A' => 'value/A', 'B' => 'value/B'],
            ['C' => 'value/C', 'D' => 'value/D', ['E' => 'value/E', ['F' => 'value/F']]],
        ]);

        $expected = $this->give_me_a_vector([
            'value/A',
            'value/B',
            'value/C',
            'value/D',
            'value/E',
            'value/F',
        ]);

        $this->assertEquals($expected, $collection->flatten());
    }

    public function test_it_can_be_flattened_to_a_depth()
    {
        $collection = $this->give_me_a_collection([
            ['value/A', ['value/B', ['value/C', ['value/D']]]],

        ]);

        $level1 = $this->give_me_a_vector([
            'value/A',
            ['value/B', ['value/C', ['value/D']]],
        ]);

        $this->assertEquals($level1, $collection->flatten(1));

        $level2 = $this->give_me_a_vector([
            'value/A',
            'value/B',
            ['value/C', ['value/D']],
        ]);

        $this->assertEquals($level2, $collection->flatten(2));

        $level3 = $this->give_me_a_vector([
            'value/A',
            'value/B',
            'value/C',
            ['value/D'],
        ]);

        $this->assertEquals($level3, $collection->flatten(3));
    }

    public function test_it_can_be_map_and_flat()
    {
        $collection = $this->give_me_a_collection([
            'A' => 'value/A',
            'B' => 'value/B',
        ]);

        $expected = $this->give_me_a_vector([
            'value/A',
            'value/A',
            'value/A',
            'value/B',
            'value/B',
            'value/B',
        ]);

        $callback = fn ($value, $key) => array_fill(0, 3, $value);
        $this->assertEquals($expected, $collection->flatMap($callback));
    }

    public function test_it_can_be_collapsed()
    {
        $collection = $this->give_me_a_collection([
            'A' => [
                'A' => [
                    'A' => [
                        'A' => 'A',
                    ],
                ],
            ],
            'B' => [
                'B' => [
                    'B' => [
                        'B' => 'B',
                    ],
                ],
            ],
        ]);

        $all = $this->give_me_a_collection([
            "A/A/A/A" => "A",
            "B/B/B/B" => "B",
        ]);

        $this->assertEquals($all, $collection->collapse());


        $dotted = $this->give_me_a_collection([
            "A.A.A.A" => "A",
            "B.B.B.B" => "B",
        ]);

        $this->assertEquals($dotted, $collection->collapse(PHP_INT_MAX, '.'));

        $level2 = $this->give_me_a_collection([
            "A/A" => [
                "A" => [
                    "A" => "A",
                ],
            ],
            "B/B" => [
                "B" => [
                    "B" => "B",
                ],
            ],
        ]);

        $this->assertEquals($level2, $collection->collapse(2));


        $level3 = $this->give_me_a_collection([
            "A/A/A" => [
                "A" => "A",
            ],
            "B/B/B" => [
                "B" => "B",
            ],
        ]);

        $this->assertEquals($level3, $collection->collapse(3));
    }

    public function test_it_can_be_shuffle()
    {
        $input = range(0, 100);
        shuffle($input);
        $input = array_slice($input, 0, 50);

        $collection = $this->give_me_a_collection($input);

        $original = array_values($collection->toArray());
        $shuffled = array_values($collection->shuffle()->toArray());

        $this->assertNotEquals($original, $shuffled);

        sort($original);
        sort($shuffled);
        $this->assertEquals($original, $shuffled);
    }


    public function test_it_serializes_as_json_properly()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame('{"A":"value\/A","B":"value\/B","C":"value\/C","D":"value\/D"}', json_encode($collection));
    }

}
