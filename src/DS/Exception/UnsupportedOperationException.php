<?php

declare(strict_types=1);

namespace PlanB\DS\Exception;

use RuntimeException;

final class UnsupportedOperationException extends RuntimeException
{
    private function __construct(mixed $methodName)
    {
        $message = "$methodName method isn't supported in empty collections";
        parent::__construct($message);
    }

    public static function make(string $methodName): self
    {
        return new self($methodName);
    }
}
