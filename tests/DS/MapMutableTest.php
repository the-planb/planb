<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Exception\InvalidElementType;
use PlanB\Tests\DS\Traits\ObjectMother;

final class MapMutableTest extends TestCase
{
    use ObjectMother;

    public function test_its_possible_to_add_some_new_elements()
    {
        $map = $this->give_me_a_mutable_and_typed_map();
        $this->assertTrue($map->isEmpty());

        $map->putAll($this->give_me_an_array());
        $this->assertCount(4, $map);

        $map->put('E', 'value/E');
        $this->assertCount(5, $map);

        $this->expectException(InvalidElementType::class);
        $this->expectExceptionMessage("The element is of type 'integer' but only 'string' is allowed");
        $map->put('F', 45);
    }

    public function test_its_possible_to_update_a_value()
    {
        $map = $this->give_me_a_mutable_and_typed_map();
        $this->assertTrue($map->isEmpty());

        $map->putAll($this->give_me_an_array());

        $map->put('A', 'value/A2');
        $this->assertCount(4, $map);

        $this->assertEquals('value/A2', $map->get('A'));
    }

    public function test_it_is_possible_to_remove_an_element_by_key()
    {
        $map = $this->give_me_a_mutable_and_typed_map();
        $this->assertTrue($map->isEmpty());

        $map->putAll([
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $map->remove('B');
        $this->assertSame([
            'A' => 'value/A',
            'C' => 'value/C',
        ], $map->toArray());
    }

    public function test_it_is_possible_to_remove_an_element_by_value()
    {
        $map = $this->give_me_a_mutable_and_typed_map();
        $this->assertTrue($map->isEmpty());

        $map->putAll([
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
        ]);

        $map->removeValue('value/B');
        $this->assertSame([
            'A' => 'value/A',
            'C' => 'value/C',
        ], $map->toArray());

        $map->removeValue('value/X');
        $this->assertSame([
            'A' => 'value/A',
            'C' => 'value/C',
        ], $map->toArray());
    }

}
