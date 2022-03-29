<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\Exception;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Exception\UnsupportedOperationException;

final class UnsupportedOperationExceptionTest extends TestCase
{
    public function test_it_is_instantiable()
    {
        $exception = UnsupportedOperationException::make('method');
        $this->assertInstanceOf(UnsupportedOperationException::class, $exception);
    }

    public function test_it_returns_the_right_message()
    {
        $exception = UnsupportedOperationException::make('head');
        $this->assertSame("head method isn't supported in empty collections", $exception->getMessage());
    }
}
