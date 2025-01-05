<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\Exception;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Exception\InvalidElementType;

final class InvalidElementTypeTest extends TestCase
{
    public function test_it_is_instantiable()
    {
        $exception = InvalidElementType::make($this, []);
        $this->assertInstanceOf(InvalidElementType::class, $exception);
    }

    public function test_it_returns_the_right_message_with_multiple_types()
    {
        $exception = InvalidElementType::make([], ['string', 'int']);
        $message   = "The element is of type 'array' but only [string, int] are allowed";

        $this->assertSame($message, $exception->getMessage());
    }

    public function test_it_returns_the_right_message_with_a_single_type()
    {
        $exception = InvalidElementType::make([], ['string']);
        $message   = "The element is of type 'array' but only 'string' is allowed";

        $this->assertSame($message, $exception->getMessage());
    }
}
