<?php

declare(strict_types=1);

namespace PlanB\DS\Map\Traits;

use PlanB\DS\Map\Map;
use PlanB\DS\Traits\CollectionTrait;
use PlanB\DS\Vector\Vector;

/**
 * @template Key of string|int
 * @template Value
 */
trait MapTrait
{
    /**
     * @use CollectionTrait<Key, Value>
     */
    use CollectionTrait;

    //INFO

    /**
     * @param Key $key
     * @return bool
     */
    public function hasKey(int|string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return Vector<Value>
     */
    public function values(): Vector
    {
        return new Vector(array_values($this->data));
    }

    /**
     * @return Vector<Key>
     */
    public function keys(): Vector
    {
        return new Vector(array_keys($this->data));
    }

    /**
     * @param Value[] $input
     */
    public function merge(iterable $input): static
    {
        $data = array_merge($this->toArray(), iterable_to_array($input));

        return $this->replicate($data);
    }

    // MODIFICATION

    /**
     * @param Value $value
     * @param Key $key
     * @return Key
     */
    public function normalizeKey(mixed $value, string|int $key): string|int
    {
        return $key;
    }

    /**
     * @template ReturnType
     * @param callable(Value, Key): ReturnType $callback
     * @return Map<ReturnType>
     */
    public function map(callable $callback): Map
    {
        $input = [];
        foreach ($this as $key => $value) {
            $input[$key] = $callback($value, $key);
        }

        return new Map($input);
    }

    /**
     * @param callable(Value, Key): Key $callback
     */
    public function mapKeys(callable $callback): static
    {
        $input = [];
        foreach ($this as $key => $value) {
            $newKey = $callback($value, $key);
            $input[$newKey] = $value;
        }

        return $this->replicate($input);
    }

    /**
     * @param null|callable(Key, Key): int $comparison
     */
    public function ksort(?callable $comparison = null): static
    {
        $data = $this->toArray();

        if (null === $comparison) {
            ksort($data);

            return $this->replicate($data);
        }

        uksort($data, $comparison);

        return $this->replicate($data);
    }

    /**
     * @param Value[] $input
     * @param null|callable(Key, Key): int $comparison
     */
    public function diffKeys(iterable $input, ?callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_diff_key($this->data, $input);

            return $this->replicate($data);
        }

        $data = array_diff_ukey($this->data, $input, $comparison);

        return $this->replicate($data);
    }

    /**
     * @param Value[] $input
     * @param null|callable(Value, Value): int $comparison
     */
    public function intersect(iterable $input, ?callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_intersect($this->data, $input);

            return $this->replicate($data);
        }

        $data = array_uintersect($this->data, $input, $comparison);

        return $this->replicate($data);
    }

    /**
     * @param Value[] $input
     * @param null|callable(Key, Key): int $comparison
     */
    public function intersectKeys(iterable $input, ?callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_intersect_key($this->data, $input);

            return $this->replicate($data);
        }

        $data = array_intersect_ukey($this->data, $input, $comparison);

        return $this->replicate($data);
    }
}
