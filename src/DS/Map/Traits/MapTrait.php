<?php

declare(strict_types=1);

namespace PlanB\DS\Map\Traits;

use PlanB\DS\Map\Map;
use PlanB\DS\Sequence\Sequence;
use PlanB\DS\Traits\CollectionTrait;

trait MapTrait
{
    use CollectionTrait;

    //INFO
    public function hasKey(mixed $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function values(): Sequence
    {
        return new Sequence(array_values($this->data));
    }

    public function keys(): Sequence
    {
        return new Sequence(array_keys($this->data));
    }

    public function merge(iterable $input): static
    {
        $data = array_merge($this->toArray(), iterable_to_array($input));

        return $this->replicate($data);
    }

    // MODIFICATION
    public function normalizeKey(mixed $value, mixed $key): mixed
    {
        return $key;
    }

    public function map(callable $callback): Map
    {
        $input = [];
        foreach ($this as $key => $value) {
            $input[$key] = $callback($value, $key);
        }

        return new Map($input);
    }

    public function mapKeys(callable $callback): static
    {
        $input = [];
        foreach ($this as $key => $value) {
            $newKey = $callback($value, $key);
            $input[$newKey] = $value;
        }

        return $this->replicate($input);
    }

    public function ksort(callable $comparison = null): static
    {
        $data = $this->toArray();

        if (null === $comparison) {
            ksort($data);

            return $this->replicate($data);
        }

        uksort($data, $comparison);

        return $this->replicate($data);
    }

    public function diffKeys(iterable $input, callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_diff_key($this->data, $input);

            return $this->replicate($data);
        }

        $data = array_diff_ukey($this->data, $input, $comparison);

        return $this->replicate($data);
    }

    public function intersect(iterable $input, callable $comparison = null): static
    {
        $input = iterable_to_array($input);
        if (is_null($comparison)) {
            $data = array_intersect($this->data, $input);

            return $this->replicate($data);
        }

        $data = array_uintersect($this->data, $input, $comparison);

        return $this->replicate($data);
    }

    public function intersectKeys(iterable $input, callable $comparison = null): static
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
