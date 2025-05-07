<?php

declare(strict_types=1);

namespace PlanB\DS\Traits;

use Exception;
use JetBrains\PhpStorm\Pure;
use PlanB\DS\Exception\ElementNotFoundException;
use PlanB\DS\Map\Map;
use PlanB\DS\Vector\Vector;
use Traversable;

/**
 * @template Key of string|int
 * @template Value
 */
trait CollectionTrait
{
    public function normalize(callable ...$callback): static
    {
        $input = $this->toArray();
        foreach ($callback as $normalizer) {
            $input = array_map($normalizer, $input);
        }

        return new static($input);
    }

    //CORE

    /**
     * @return Value[]
     */
    public function toArray(): array
    {
        return $this->data;
    }

    private function replicate(iterable $input = []): static
    {
        /** @phpstan-ignore-next-line */
        return new static($input, null, $this->types);
    }

    /**
     * @return Value[]
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function getAllowedTypes(): array
    {
        return $this->types;
    }

    /**
     * @return Traversable<Key, Value>
     */
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

    /**
     * @return Value
     */
    public function first(): mixed
    {
        $key = array_key_first($this->data);

        return $this->data[$key];
    }

    /**
     * @return Value
     * @throws Exception
     */
    public function firstThat(callable $condition): mixed
    {
        foreach ($this->getIterator() as $key => $value) {
            if ($condition($value, $key)) {
                return $value;
            }
        }
        return null;
    }

    #[Pure]
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * @template Default
     *
     * @param Key $key
     * @param Default $default
     * @return Value|Default
     */
    public function get(string|int $key, mixed $default = null): mixed
    {
        if (1 === func_num_args()) {
            return $this->data[$key] ?? throw ElementNotFoundException::missingKey($key);
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * @return Value
     */
    public function last(): mixed
    {
        $key = array_key_last($this->data);

        return $this->data[$key] ?? null;
    }

    /**
     * @param callable(Value, Key): bool $condition
     * @return Value
     */
    public function lastThat(callable $condition): mixed
    {
        $data = array_reverse($this->data);
        foreach ($data as $key => $value) {
            if ($condition($value, $key)) {
                return $value;
            }
        }
        return null;
    }

    /**
     */
    public function init(): static
    {
        $numOfElements = $this->count() - 1;

        return $this->take($numOfElements);
    }

    public function take(int $numOfElements): static
    {
        $length = abs($numOfElements);
        $offset = $numOfElements < 0 ? $this->count() - $length : 0;

        $input = array_slice($this->data, $offset, $length, true);

        return $this->replicate($input);
    }

    /**
     */
    public function tail(): static
    {
        return $this->drop(1);
    }

    /**
     */
    public function drop(int $numOfElements): static
    {
        $length = $this->count() - abs($numOfElements);
        $offset = max($numOfElements, 0);

        $input = array_slice($this->data, $offset, $length, true);
        return $this->replicate($input);
    }

    /**
     * @param callable(Value, Key): bool $condition
     */
    public function takeWhile(callable $condition): static
    {
        $index = $this->findIndex($condition);

        return $this->take($index);
    }

    private function findIndex(callable $condition): int
    {
        $index = 0;
        foreach ($this as $key => $value) {
            if (!$condition($value, $key)) {
                break;
            }
            $index++;
        }

        return $index;
    }

    /**
     * @param callable(Value, Key): bool $condition
     */
    public function dropWhile(callable $condition): static
    {
        $index = $this->findIndex($condition);

        return $this->drop($index);
    }

    //INFO

    /**
     * @param Value $value
     * @return bool
     */
    public function hasValue(mixed $value): bool
    {
        return in_array($value, $this->data);
    }

    /**
     * @param Value $value
     * @return Key|null
     */
    public function find(mixed $value): string|int|null
    {
        $key = array_search($value, $this->data);

        return $key !== false ? $key : null;
    }

    /**
     * @param Value ...$values
     * @return bool
     */
    public function contains(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (!in_array($value, $this->data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param callable(Value, Key): bool $condition
     * @return bool
     */
    public function some(callable $condition): bool
    {
        foreach ($this as $key => $value) {
            if ($condition($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable(Value, Key): bool $condition
     * @return bool
     */
    public function every(callable $condition): bool
    {
        foreach ($this as $key => $value) {
            if (!$condition($value, $key)) {
                return false;
            }
        }

        return true;
    }

    //MODIFICATION

    /**
     * @param callable(Value, Key):mixed $callback
     */
    public function each(callable $callback): static
    {
        foreach ($this as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    /**
     * @param null|callable(Value, Key): bool $condition
     */
    public function filter(?callable $condition = null): static
    {
        if (null === $condition) {
            $input = array_filter($this->data);

            return $this->replicate($input);
        }

        $input = array_filter($this->data, $condition, ARRAY_FILTER_USE_BOTH);

        return $this->replicate($input);
    }

    /**
     * @param null|callable(Value, Key): int $comparison
     */
    public function sort(?callable $comparison = null): static
    {
        $data = $this->toArray();

        if (null === $comparison) {
            asort($data);

            return $this->replicate($data);
        }

        uasort($data, $comparison);

        return $this->replicate($data);
    }

    /**
     * @param Value[] $input
     * @param null|callable(Value, Value): int $comparison
     */
    public function diff(iterable $input, ?callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_diff($this->data, $input);

            return $this->replicate($data);
        }

        $data = array_udiff($this->data, $input, $comparison);

        return $this->replicate($data);
    }

    /**
     * @param null|callable(Value, Key): mixed $callback
     * @param bool $strict
     */
    public function unique(?callable $callback = null, bool $strict = false): static
    {
        $callback ??= fn (mixed $item, string|int $key) => $item;

        $temp = [];
        $keys = [];
        foreach ($this as $key => $value) {
            $normalized = $callback($value, $key);

            if (!in_array($normalized, $temp, $strict)) {
                $temp[] = $normalized;
                $keys[] = $key;
            }
        }

        return $this->filter(fn (mixed $item, mixed $key) => in_array($key, $keys));
    }

    /**
     */
    public function reversed(): static
    {
        $data = array_reverse($this->toArray(), true);

        return $this->replicate($data);
    }

    /**
     * @template Initial
     * @param callable $callback
     * @param Initial $initial
     * @return Initial
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $carry = $initial;
        foreach ($this as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }

        return $carry;
    }

    public function flatMap(callable $callback, int $depth = PHP_INT_MAX): Vector
    {
        return Map::collect($this->toArray())
            ->map($callback)
            ->flatten($depth);
    }

    public function flatten(int $depth = PHP_INT_MAX): Vector
    {
        $temp = array_flatten($this->toArray(), $depth);

        return Vector::collect($temp);
    }

    public function collapse(int $depth = PHP_INT_MAX, string $glue = DIRECTORY_SEPARATOR): static
    {
        $temp = array_collapse($this->toArray(), $depth, $glue);

        return $this->replicate($temp);
    }

    /**
     */
    public function shuffle(): static
    {
        $temp = $this->toArray();
        uksort($temp, fn () => rand() - rand());

        return $this->replicate($temp);
    }

}
