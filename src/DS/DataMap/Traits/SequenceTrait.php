<?php

declare(strict_types=1);

namespace PlanB\DS\DataMap\Traits;

use JetBrains\PhpStorm\Pure;
use PlanB\DS\DataMap\Exception\InvalidElementType;
use Traversable;

trait SequenceTrait
{
    public readonly int $length;
    private ?array $types = null;

    public static function collect(iterable $input = [], string ...$types): static
    {
        return new static($input, ...$types);
    }

    public static function empty(string ...$types): static
    {
        return new self([], ...$types);
    }

    public function types(): array
    {
        return $this->types ?? [];
    }

    public function count(): int
    {
        return $this->length;
    }

    #[Pure]
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function isEmpty(): bool
    {
        return 0 === $this->length;
    }

    public function any(callable $condition): bool
    {
        $temp = false;
        foreach ($this->data as $key => $value) {
            $temp = $temp || $condition($value, $key);
        }

        return $temp;
    }

    public function all(callable $condition): bool
    {
        $temp = true;
        foreach ($this->data as $key => $value) {
            $temp = $temp && $condition($value, $key);
        }

        return $temp;
    }

    public function each(callable $condition): static
    {
        foreach ($this->data as $key => $value) {
            $condition($value, $key);
        }

        return $this;
    }

    public function filter(callable $callback = null): static
    {
        $filter = $callback ?? static fn ($item) => ! empty($item);
        $temp   = [];
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                $temp[$key] = $value;
            }
        }

        return $this->copy($temp);
    }

    private function copy(iterable $data = []): static
    {
        $types = $this->types ?? [];

        return new static($data, ...$types);
    }

    public function sort(callable $callback = null): static
    {
        $callback ??= static fn ($first, $second) => $first <=> $second;
        $data     = $this->data->sorted($callback);

        return $this->copy($data);
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $carry = $initial;

        foreach ($this->data as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }

        return $carry;
    }


    public function merge(iterable $input): static
    {
        $merged = $this->data->merge($input);

        return $this->copy($merged);
    }


    public function toArray(): array
    {
        return $this->data->toArray();
    }

    public function getIterator(): Traversable
    {
        foreach ($this->data as $key => $value) {
            yield $key => $value;
        }
    }

    private function assert(iterable $input): void
    {
        foreach ($input as $value) {
            $this->types ??= (array)type_of($value);
            is_of_the_type($value, ...$this->types) || throw InvalidElementType::make($value, $this->types);
        }
    }
}
