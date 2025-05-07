<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use PHPUnit\Framework\TestCase;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Map\MapImmutableInterface;
use PlanB\DS\Vector\Vector;
use PlanB\DS\Vector\VectorImmutableInterface;
use PlanB\DS\Vector\VectorInterface;
use PlanB\Tests\DS\Traits\Assertions;
use PlanB\Tests\DS\Traits\ObjectMother;

use function array_is_list;

final class VectorTest extends TestCase
{
    use ObjectMother;
    use Assertions;

    //CORE
    public function test_it_can_be_instantiate()
    {
        $vector = Vector::collect();
        $this->assertInstanceOf(Vector::class, $vector);
        $this->assertInstanceOf(CollectionInterface::class, $vector);
        $this->assertInstanceOf(VectorInterface::class, $vector);
    }

    public function test_it_can_be_instantiate_using_a_mapping_function()
    {
        $vector = Vector::collect([1, 2, 3], fn (int $i) => str_repeat('*', $i));
        $this->assertEquals([
            '*',
            '**',
            '***',
        ], $vector->toArray());
    }

    public function test_it_is_a_array_list()
    {
        $vector = $this->give_me_a_vector();

        $this->assertTrue(array_is_list($vector->toArray()));
    }

    //INFO
    public function test_it_knows_if_has_an_index()
    {
        $vector = $this->give_me_a_vector();

        $this->assertTrue($vector->hasIndex(3));
        $this->assertFalse($vector->hasIndex(4));
    }

    //MODIFICATION
    public function test_it_can_be_mapped()
    {
        $vector = $this->give_me_a_vector();
        $expected = $this->give_me_a_vector([
            'VALUE/A',
            'VALUE/B',
            'VALUE/C',
            'VALUE/D',
        ]);

        $callback = fn (string $value, int $key) => strtoupper($value);
        $mapped = $vector->map($callback);

        $this->assertEquals($expected, $mapped);
        $this->assertInstanceOf(VectorImmutableInterface::class, $mapped);

        $this->assertCollectionHasNotChange($vector);
    }

    public function test_it_can_be_transform_into_a_map_properly()
    {
        $vector = $this->give_me_a_vector();

        $map = $vector->toMap(fn (string $value) => substr($value, -1));

        $expected = $this->give_me_a_map([
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
            'D' => 'value/D',
        ]);

        $this->assertEquals($expected, $map);
        $this->assertInstanceOf(MapImmutableInterface::class, $map);
    }
}
