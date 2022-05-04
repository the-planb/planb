<?php

declare(strict_types=1);

namespace PlanB\Validation\Traits\Exception;

use RuntimeException;

final class ConstraintNotFoundException extends RuntimeException
{
    public static function make(string $className, string $constraintName): self
    {
        $message = "Class '$className' hasn't an associated Constraint: '$constraintName' was expected";

        return new self($message);
    }
}
