<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\Exception;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Exception\ElementNotFoundException;

final class ElementNotFoundExceptionTest extends TestCase
{
    public function test_it_is_instantiable_for_keys()
    {
        $exception = ElementNotFoundException::missingKey('key');
        $this->assertInstanceOf(ElementNotFoundException::class, $exception);
        $this->assertSame("The key 'key' doesn't exists", $exception->getMessage());
    }

    public function test_it_is_instantiable_for_index()
    {
        $exception = ElementNotFoundException::missingIndex(10);
        $this->assertInstanceOf(ElementNotFoundException::class, $exception);
        $this->assertSame("The index '10' doesn't exists", $exception->getMessage());
    }

    public function test_it_is_instantiable_for_condition()
    {
        $exception = ElementNotFoundException::condition();
        $this->assertInstanceOf(ElementNotFoundException::class, $exception);
        $this->assertSame("No element meets the condition", $exception->getMessage());
    }
}
