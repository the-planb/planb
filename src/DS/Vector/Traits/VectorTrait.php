<?php

declare(strict_types=1);

namespace PlanB\DS\Vector\Traits;

use PlanB\DS\Map\Map;
use PlanB\DS\Traits\CollectionTrait;
use PlanB\DS\Vector\Vector;

/**
 * @template Key of int
 * @template Value
 */
trait VectorTrait
{
    /**
     * @use CollectionTrait<Key, Value>
     */
    use CollectionTrait;

    /**
     * @template ReturnType
     * @param callable(Value, Key): ReturnType $callback
     * @return Vector<ReturnType>
     */
    public function map(callable $callback): Vector
    {
        $input = [];
        foreach ($this as $key => $value) {
            $input[$key] = $callback($value, $key);
        }

        return new Vector($input);
    }

    public function hasIndex(int $index): bool
    {
        return array_key_exists($index, $this->data);
    }

    /**
     * @param callable(Value, Key): Key $callback
     * @return Map<Value>
     */
    public function toMap(callable $callback): Map
    {
        return (new Map($this->data, null, $this->types))
            ->mapKeys($callback);
    }

}
