<?php

namespace PlanB\Tests\Text;

use PHPUnit\Framework\TestCase;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Vector\VectorInterface;
use PlanB\Text\TextList;

class TextListTest extends TestCase
{
    public function test_it_can_be_instantiate()
    {
        $text = TextList::collect();
        $this->assertInstanceOf(TextList::class, $text);
        $this->assertInstanceOf(CollectionInterface::class, $text);
        $this->assertInstanceOf(VectorInterface::class, $text);
    }

    public function test_it_implode_a_string_list_properly()
    {
        $text = TextList::collect(['a', 'b', 'c'], fn (string $value) => strtoupper($value));
        $this->assertEquals('ABC', $text->implode(''));
    }
}
