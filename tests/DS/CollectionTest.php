<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use DateTime;
use PHPUnit\Framework\TestCase;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Exception\ElementNotFoundException;
use PlanB\DS\Exception\InvalidElementType;
use PlanB\DS\Exception\UnsupportedOperationException;
use PlanB\DS\Sequence\Sequence;
use PlanB\Tests\DS\Traits\Assertions;
use PlanB\Tests\DS\Traits\ObjectMother;
use stdClass;

final class CollectionTest extends TestCase
{
    use ObjectMother;

    use Assertions;

    public function test_it_extracts_the_types_from_the_elementType_attribute_properly()
    {
        $collection = $this->give_me_a_typed_collection();

        $this->assertEquals([
            'string',
            DateTime::class,

        ], $collection->getAllowedTypes());
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
        $collection = Sequence::collect([
            'value/A',
            'value/B',
        ]);

        $this->assertInstanceOf(CollectionInterface::class, $collection);
    }

    public function test_it_is_instantiable_using_a_traversable_like_input()
    {
        $data = $this->give_me_an_array();
        $input = $this->give_me_a_collection($data);
        $collection = $this->give_me_a_collection($input);

        $this->assertEquals($data, $collection->toArray());
        $this->assertInstanceOf(CollectionInterface::class, $collection);
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

    /** @dataProvider countProvider */
    public function test_it_knows_how_many_elements_has(array $input, int $total, $empty)
    {
        $collection = $this->give_me_a_collection($input);

        $this->assertSame($total, $collection->count());
        $this->assertSame($empty, $collection->isEmpty());
        $this->assertSame(! $empty, $collection->isNotEmpty());
    }

    //COUNT

    public function countProvider()
    {
        $data = fn(int $total) => array_fill(0, $total, '*');

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

    public function test_it_gets_the_head_element()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame('value/A', $collection->head());
        $this->assertCollectionHasNotChange($collection);
    }


    public function test_it_throws_an_exception_when_call_head_method_on_an_empty_collection()
    {
        $collection = $this->give_me_an_empty_collection();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage("head method isn't supported in empty collections");
        $collection->head();
    }

    public function test_it_throws_an_exception_when_call_first_method_on_an_empty_collection()
    {
        $collection = $this->give_me_an_empty_collection();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage("first method isn't supported in empty collections");
        $collection->first();
    }

    public function test_first_method_is_an_alias_of_head_method()
    {
        $collection = $this->give_me_a_collection();

        $this->assertSame($collection->head(), $collection->first());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_gets_the_first_element_that_meets_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn(string $value) => str_ends_with($value, 'B');
        $this->assertSame('value/B', $collection->firstThat($condition));

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_firstThat_method_throws_an_exception_when_no_element_meets_the_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn(string $value) => str_ends_with($value, 'X');

        $this->expectExceptionMessage("No element meets the condition");
        $collection->firstThat($condition);
    }

    public function test_firstThat_method_throws_an_exception_when_collection_is_empty()
    {
        $collection = $this->give_me_a_collection([]);

        $condition = fn(string $value) => str_ends_with($value, 'X');

        $this->expectExceptionMessage("firstThat method isn't supported in empty collections");
        $collection->firstThat($condition);
    }

    public function test_it_gets_the_last_element()
    {
        $collection = $this->give_me_a_collection();
        $this->assertSame('value/D', $collection->last());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_throws_an_exception_when_call_last_method_on_an_empty_collection()
    {
        $collection = $this->give_me_an_empty_collection();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage("last method isn't supported in empty collections");
        $collection->last();
    }

    public function test_it_gets_the_last_element_that_meets_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn(string $value) => str_starts_with($value, 'value');
        $this->assertSame('value/D', $collection->lastThat($condition));

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_lastThat_method_throws_an_exception_when_no_element_meets_the_condition()
    {
        $collection = $this->give_me_a_collection();

        $condition = fn(string $value) => str_ends_with($value, 'X');

        $this->expectExceptionMessage("No element meets the condition");
        $collection->lastThat($condition);
    }

    public function test_lastThat_method_throws_an_exception_when_collection_is_empty()
    {
        $collection = $this->give_me_a_collection([]);

        $condition = fn(string $value) => str_ends_with($value, 'X');

        $this->expectExceptionMessage("lastThat method isn't supported in empty collections");
        $collection->lastThat($condition);
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

    public function test_it_take_method_retuns_an_empty_collection_if_param_is_zero_or_negative()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals([], $collection->take(0)->toArray());
        $this->assertEquals([], $collection->take(-1)->toArray());
        $this->assertEquals([], $collection->take(-2)->toArray());

        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_take_method_retuns_all_elemetns_if_param_is_greater_than_the_collection_size()
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

    public function test_it_drop_method_retuns_all_elemetns_if_param_is_zero_or_negative()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals($collection, $collection->drop(0));
        $this->assertNotSame($collection, $collection->drop(0));

        $this->assertEquals($collection, $collection->drop(-1));
        $this->assertNotSame($collection, $collection->drop(-1));
    }

    public function test_it_drop_method_retuns_an_empty_collection_if_param_greater_than_collection_size()
    {
        $collection = $this->give_me_a_collection();

        $this->assertEquals([], $collection->drop(10)->toArray());
        $this->assertCollectionHasNotChange($collection);
    }

    public function test_it_can_take_the_first_elements_that_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();
        $condition = fn(string $value) => $value !== 'value/C';
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
        $condition = fn(string $value) => $value !== 'value/C';
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

    public function test_it_knows_if_any_element_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $conditionA = fn(string $value) => str_ends_with($value, '/A');
        $conditionB = fn(string $value) => str_ends_with($value, '/B');
        $conditionC = fn(string $value) => str_ends_with($value, '/X');

        $this->assertTrue($collection->any($conditionA));
        $this->assertTrue($collection->any($conditionB));
        $this->assertFalse($collection->any($conditionC));
    }

    public function test_it_knows_if_all_the_elements_meet_a_condition()
    {
        $collection = $this->give_me_a_collection();

        $conditionYes = fn(string $value) => str_starts_with($value, 'value/');
        $conditionNO = fn(string $value) => str_ends_with($value, '/A');

        $this->assertTrue($collection->all($conditionYes));
        $this->assertFalse($collection->all($conditionNO));
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

        $condition = fn(string $_, string $key) => ! str_ends_with($key, '2');

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
        $comparison = fn(string $first, string $second) => strrev($first) <=> strrev($second);

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

        $expected = $this->give_me_a_sequence([
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

        $level1 = $this->give_me_a_sequence([
            'value/A',
            ['value/B', ['value/C', ['value/D']]],
        ]);

        $this->assertEquals($level1, $collection->flatten(1));

        $level2 = $this->give_me_a_sequence([
            'value/A',
            'value/B',
            ['value/C', ['value/D']],
        ]);

        $this->assertEquals($level2, $collection->flatten(2));

        $level3 = $this->give_me_a_sequence([
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

        $expected = $this->give_me_a_sequence([
            'value/A',
            'value/A',
            'value/A',
            'value/B',
            'value/B',
            'value/B',
        ]);

        $callback = fn($value, $key) => array_fill(0, 3, $value);
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


    public function test_it_can_be_passed_to_a_custom_function()
    {
        $collection = $this->give_me_a_collection();

        $callback = function ($param) use ($collection) {
            $this->assertEquals($param, $collection);
        };

        $collection = $this->give_me_a_collection();
        $collection->applyTo($callback);
    }


}



