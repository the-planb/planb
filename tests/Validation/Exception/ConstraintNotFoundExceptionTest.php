<?php

declare(strict_types=1);

namespace PlanB\Tests\Validation\Exception;

use PHPUnit\Framework\TestCase;
use PlanB\Validation\Traits\Exception\ConstraintNotFoundException;

final class ConstraintNotFoundExceptionTest extends TestCase
{
    public function test_it_is_instantiable()
    {
        $exception = ConstraintNotFoundException::make('CLASS', 'CONSTRAINT');
        $this->assertInstanceOf(ConstraintNotFoundException::class, $exception);
        $this->assertSame(
            "Class 'CLASS' hasn't an associated Constraint: 'CONSTRAINT' was expected",
            $exception->getMessage()
        );
    }
}
