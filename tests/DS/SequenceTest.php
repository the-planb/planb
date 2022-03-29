<?php

declare(strict_types=1);

namespace PlanB\Tests\DS;

use PHPUnit\Framework\TestCase;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Map\MapImmutableInterface;
use PlanB\DS\Sequence\Sequence;
use PlanB\DS\Sequence\SequenceImmutableInterface;
use PlanB\DS\Sequence\SequenceInterface;
use PlanB\Tests\DS\Traits\Assertions;
use PlanB\Tests\DS\Traits\ObjectMother;

use function array_is_list;


final class SequenceTest extends TestCase
{
    use ObjectMother;
    use Assertions;

//CORE
    public function test_it_can_be_instantiate()
    {
        $sequence = Sequence::collect();
        $this->assertInstanceOf(Sequence::class, $sequence);
        $this->assertInstanceOf(CollectionInterface::class, $sequence);
        $this->assertInstanceOf(SequenceInterface::class, $sequence);
    }

    public function test_it_is_a_array_list()
    {
        $sequence = $this->give_me_a_sequence();

        $this->assertTrue(array_is_list($sequence->toArray()));
    }

//INFO
    public function test_it_knows_if_has_an_index()
    {
        $sequence = $this->give_me_a_sequence();

        $this->assertTrue($sequence->hasIndex(3));
        $this->assertFalse($sequence->hasIndex(4));
    }

//MODIFICATION
    public function test_it_can_be_mapped()
    {
        $sequence = $this->give_me_a_sequence();
        $expected = $this->give_me_a_sequence([
            'VALUE/A',
            'VALUE/B',
            'VALUE/C',
            'VALUE/D',
        ]);

        $callback = fn(string $value, int $key) => strtoupper($value);
        $mapped = $sequence->map($callback);

        $this->assertEquals($expected, $mapped);
        $this->assertInstanceOf(SequenceImmutableInterface::class, $mapped);

        $this->assertCollectionHasNotChange($sequence);
    }

    public function test_it_can_be_transform_into_a_map_properly()
    {
        $sequence = $this->give_me_a_sequence();

        $map = $sequence->toMap(fn(string $value) => substr($value, -1));

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
