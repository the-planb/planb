<?php

declare(strict_types=1);

namespace PlanB\DS\Exception;

use RuntimeException;

final class ElementNotFoundException extends RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingKey(mixed $key): self
    {
        $message = "The key '$key' doesn't exists";

        return new self($message);
    }

    public static function missingIndex(int $index): self
    {
        $message = "The index '$index' doesn't exists";

        return new self($message);
    }

    public static function condition(): self
    {
        $message = "No element meets the condition";

        return new self($message);
    }
}
