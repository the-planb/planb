<?php
declare(strict_types=1);

namespace PlanB\Tests\Text;

use PHPUnit\Framework\TestCase;
use PlanB\Text\Text;

class TextTest extends TestCase
{
    public function test_it_is_able_to_get_words_from_a_string()
    {
        $text = Text::explode('hola,     que          pasa?');

        $this->assertEquals('hola,', $text->get(0));
        $this->assertEquals('que', $text->get(1));
        $this->assertEquals('pasa?', $text->get(2));

        $this->assertCount(3, $text);
    }

    public function test_it_is_able_to_get_words_from_a_string_with_a_limit()
    {
        $text = Text::explode('hola-que-pasa?', '-', 2);

        $this->assertEquals('hola', $text->get(0));
        $this->assertEquals('que-pasa?', $text->get(1));

        $this->assertCount(2, $text);
    }

    public function test_it_is_able_to_get_words_from_a_string_with_a_custom_separator()
    {
        $text = Text::explode('hola,|que|pasa?', '|');

        $this->assertEquals('hola,', $text->get(0));
        $this->assertEquals('que', $text->get(1));
        $this->assertEquals('pasa?', $text->get(2));

        $this->assertCount(3, $text);
    }

    public function test_it_is_able_to_get_words_from_a_string_using_a_regexpr()
    {
        $text = Text::split('hola,1que5pasa?', '/[0-9]/');

        $this->assertEquals('hola,', $text->get(0));
        $this->assertEquals('que', $text->get(1));
        $this->assertEquals('pasa?', $text->get(2));

        $this->assertCount(3, $text);
    }

    public function test_it_is_able_to_implode()
    {
        $text = Text::explode('hola,     que          pasa?');

        $this->assertEquals('hola, que pasa?', $text->implode());
    }

    public function test_it_is_able_to_implode_with_a_custom_separator()
    {
        $text = Text::explode('hola,     que          pasa?');

        $this->assertEquals('hola,-que-pasa?', $text->implode('-'));

    }

    public function test_it_is_able_to_do_complex_transformations()
    {
        $text = Text::explode('AA/BB/CC', '/')
            ->tail()
            ->implode('-');

        $this->assertEquals('BB-CC', $text);
    }
}
