<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Exception\InvalidElementType;
use PlanB\Tests\DS\Traits\ObjectMother;

final class SequenceMutableTest extends TestCase
{
    use ObjectMother;

    public function test_its_possible_to_add_some_new_elements()
    {
        $sequence = $this->give_me_a_mutable_and_typed_sequence();
        $this->assertTrue($sequence->isEmpty());

        $sequence->addAll($this->give_me_an_array());
        $this->assertCount(4, $sequence);

        $sequence->add('value/E');
        $this->assertCount(5, $sequence);

        $this->expectException(InvalidElementType::class);
        $this->expectExceptionMessage("The element is of type 'integer' but only 'string' is allowed");
        $sequence->add(45);
    }


    public function test_it_is_possible_to_update_a_value()
    {
        $sequence = $this->give_me_a_mutable_and_typed_sequence();
        $this->assertTrue($sequence->isEmpty());

        $sequence->add('A');
        $this->assertCount(1, $sequence);

        $sequence->set(0, 'B');
        $this->assertCount(1, $sequence);
        $this->assertEquals('B', $sequence->get(0));

        $sequence->set(10, 'C');
        $this->assertSame(['B', 'C'], $sequence->toArray());
    }

    public function test_it_is_possible_to_remove_an_element_by_index()
    {
        $sequence = $this->give_me_a_mutable_and_typed_sequence();
        $this->assertTrue($sequence->isEmpty());

        $sequence->addAll(['A', 'B', 'C']);
        $this->assertCount(3, $sequence);

        $sequence->remove(1);
        $this->assertSame(['A', 'C'], $sequence->toArray());
    }

    public function test_it_is_possible_to_remove_an_element_by_value()
    {
        $sequence = $this->give_me_a_mutable_and_typed_sequence();
        $this->assertTrue($sequence->isEmpty());

        $sequence->addAll(['A', 'B', 'C']);
        $this->assertCount(3, $sequence);

        $sequence->removeValue('B');
        $this->assertSame(['A', 'C'], $sequence->toArray());


        $sequence->removeValue('X');
        $this->assertSame(['A', 'C'], $sequence->toArray());
    }

    public function test_it_is_possible_to_insert_some_elements_in_a_position()
    {
        $sequence = $this->give_me_a_mutable_and_typed_sequence();
        $sequence->addAll(['A', 'B', 'C']);

        $sequence->insert(1, 'X', 'Y', 'Z');
        $this->assertSame([
            'A',
            'X',
            'Y',
            'Z',
            'B',
            'C',
        ], $sequence->toArray());
    }
}
