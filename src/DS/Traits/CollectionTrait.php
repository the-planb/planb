<?php

declare(strict_types=1);

namespace PlanB\DS\Traits;

use JetBrains\PhpStorm\Pure;
use PlanB\DS\Attribute\ElementType;
use PlanB\DS\Exception\ElementNotFoundException;
use PlanB\DS\Exception\InvalidElementType;
use PlanB\DS\Exception\UnsupportedOperationException;
use PlanB\DS\Map\Map;
use PlanB\DS\Sequence\Sequence;
use Traversable;

trait CollectionTrait
{
    //CORE
    /**
     * @var string[]
     */
    private readonly array $types;

    public function __construct(iterable $input = [])
    {
        $elementType = ElementType::fromClass(static::class);
        $this->types = $elementType->getTypes();

        $input = iterable_to_array($input);
        $input = $this->before($input);

        $this->assert($input);

        $this->data = $this->ensureData($input);
    }

    public function before(array $input): array
    {
        return $input;
    }

    private function assert(iterable $input): void
    {
        if (0 === count($this->types)) {
            return;
        }
        foreach ($input as $value) {
            is_of_the_type($value, ...$this->types) || throw InvalidElementType::make($value, $this->types);
        }
    }

    abstract private function ensureData(array $input): array;

    public static function collect(iterable $input = []): static
    {
        return new static($input);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return string[]
     */
    public function getAllowedTypes(): array
    {
        return $this->types;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->data as $key => $value) {
            yield $key => $value;
        }
    }

    //COUNT
    #[Pure]
    public function isNotEmpty(): bool
    {
        return 0 !== $this->count();
    }

    public function count(): int
    {
        return count($this->data);
    }

    //GETTERS

    public function first(): mixed
    {
        if ($this->isEmpty()) {
            throw UnsupportedOperationException::make('first');
        }

        return $this->head();
    }

    public function head(): mixed
    {
        if ($this->isEmpty()) {
            throw UnsupportedOperationException::make('head');
        }

        $key = array_key_first($this->data);

        return $this->data[$key];
    }

    public function firstThat(callable $condition): mixed
    {
        if ($this->isEmpty()) {
            throw UnsupportedOperationException::make('firstThat');
        }

        foreach ($this as $key => $value) {
            if ($condition($value, $key)) {
                return $value;
            }
        }

        throw ElementNotFoundException::condition();
    }

    #[Pure]
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        if (1 === func_num_args()) {
            return $this->data[$key] ?? throw ElementNotFoundException::missingKey($key);
        }

        return $this->data[$key] ?? $default;
    }

    public function last(): mixed
    {
        if ($this->isEmpty()) {
            throw UnsupportedOperationException::make('last');
        }

        $key = array_key_last($this->data);

        return $this->data[$key];
    }

    public function lastThat(callable $condition): mixed
    {
        if ($this->isEmpty()) {
            throw UnsupportedOperationException::make('lastThat');
        }

        $data = array_reverse($this->data);
        foreach ($data as $key => $value) {
            if ($condition($value, $key)) {
                return $value;
            }
        }

        throw ElementNotFoundException::condition();
    }


    public function init(): static
    {
        $numOfElements = $this->count() - 1;

        return $this->take($numOfElements);
    }

    public function take(int $numOfElements): static
    {
        $numOfElements = $numOfElements >= 1 ? $numOfElements : 0;

        $input = array_slice($this->data, 0, $numOfElements, true);

        return new static($input);
    }

    public function tail(): static
    {
        return $this->drop(1);
    }

    public function drop(int $numOfElements): static
    {
        $numOfElements = $numOfElements >= 1 ? $numOfElements : 0;
        $length = $this->count() - $numOfElements;

        $input = array_slice($this->data, $numOfElements, $length, true);

        return new static($input);
    }

    public function takeWhile(callable $condition): static
    {
        $index = $this->findIndex($condition);

        return $this->take($index);
    }

    private function findIndex(callable $condition): int
    {
        $index = 0;
        foreach ($this as $key => $value) {
            if (! $condition($value, $key)) {
                break;
            }
            $index++;
        }

        return $index;
    }

    public function dropWhile(callable $condition): static
    {
        $index = $this->findIndex($condition);

        return $this->drop($index);
    }

    //INFO
    public function hasValue(mixed $value): bool
    {
        return in_array($value, $this->data);
    }

    public function find(mixed $value): mixed
    {
        $key = array_search($value, $this->data);

        return $key !== false ? $key : null;
    }

    public function contains(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (! in_array($value, $this->data)) {
                return false;
            }
        }

        return true;
    }

    public function any(callable $condition): bool
    {
        foreach ($this as $key => $value) {
            if ($condition($value, $key)) {
                return true;
            }
        }

        return false;
    }

    public function all(callable $condition): bool
    {
        foreach ($this as $key => $value) {
            if (! $condition($value, $key)) {
                return false;
            }
        }

        return true;
    }

    //MODIFICATION
    public function each(callable $callback): static
    {
        foreach ($this as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    public function filter(callable $condition = null): static
    {
        if (null === $condition) {
            $input = array_filter($this->data);

            return new static($input);
        }

        $input = array_filter($this->data, $condition, ARRAY_FILTER_USE_BOTH);

        return new static($input);
    }

    public function sort(callable $comparison = null): static
    {
        $data = $this->toArray();

        if (null === $comparison) {
            asort($data);

            return new static($data);
        }

        uasort($data, $comparison);

        return new static($data);
    }

    public function diff(iterable $input, callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_diff($this->data, $input);

            return new static($data);
        }

        $data = array_udiff($this->data, $input, $comparison);

        return new static($data);
    }

    public function unique(int $flags = SORT_STRING): static
    {
        $input = array_unique($this->data, $flags);

        return new static($input);
    }

    public function reversed(): static
    {
        $data = array_reverse($this->toArray(), true);

        return new static($data);
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $carry = $initial;
        foreach ($this as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }

        return $carry;
    }

    public function flatMap(callable $callback, int $depth = PHP_INT_MAX): Sequence
    {
        return Map::collect($this->toArray())
            ->map($callback)
            ->flatten($depth);
    }

    public function flatten(int $depth = PHP_INT_MAX): Sequence
    {
        $temp = array_flatten($this->toArray(), $depth);

        return Sequence::collect($temp);
    }

    public function collapse(int $depth = PHP_INT_MAX, string $glue = DIRECTORY_SEPARATOR): static
    {
        $temp = array_collapse($this->toArray(), $depth, $glue);

        return static::collect($temp);
    }

    public function applyTo(callable $callback): mixed
    {
        return $callback($this);
    }
}
