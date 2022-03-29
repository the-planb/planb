<?php

declare(strict_types=1);

namespace PlanB\DS\Exception;

use RuntimeException;

final class InvalidElementType extends RuntimeException
{
    private function __construct(mixed $value, array $expected)
    {
        $message = $this->parseMessage($value, $expected);

        parent::__construct($message);
    }

    private function parseMessage(mixed $value, array $expected): string
    {
        $pattern = "The element is of type '%s' but only '%s' is allowed";
        if (count($expected) > 1) {
            $pattern = "The element is of type '%s' but only [%s] are allowed";
        }

        $types = implode(', ', $expected);

        return sprintf($pattern, type_of($value), $types);
    }

    public static function make(mixed $value, array $expected): self
    {
        return new self($value, $expected);
    }
}
