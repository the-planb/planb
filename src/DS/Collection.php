<?php

declare(strict_types=1);

namespace PlanB\DS;

use PlanB\DS\Attribute\ElementType;
use PlanB\DS\Exception\InvalidElementType;
use PlanB\DS\Map\MapInterface;
use PlanB\DS\Traits\CollectionTrait;
use PlanB\DS\Vector\VectorInterface;
use Throwable;

/**
 * @template Key of string|int
 * @template Value
 * @phpstan-consistent-constructor
 */
abstract class Collection implements CollectionInterface
{
    /**
     * @use CollectionTrait<Key, Value>
     */
    use CollectionTrait;

    /**
     * @var string[]
     */
    protected readonly array $types;

    protected array $data;

    /**
     * @param Value[] $input
     * @param string[] $types
     */
    public function __construct(iterable $input = [], ?callable $mapping = null, array $types = [])
    {
        $this->types = ElementType::fromClass(static::class)
            ->merge(...$types)
            ->getTypes();

        $input = iterable_to_array($input);
        $input = is_callable($mapping) ?
            array_map($mapping, array_values($input), array_keys($input)) :
            $input;

        $this->data = $this->sanitize($input);
    }

    public static function collect(iterable $input = [], ?callable $mapping = null): static
    {
        return new static($input, $mapping);
    }

    public static function tryFrom(iterable $input = [], ?callable $mapping = null): null|static
    {
        try {
            return new static($input, $mapping);
        } catch (Throwable) {
            return null;
        }
    }


    public static function fromCartesian(callable $callback, iterable ...$inputs): static
    {
        $cartesian = cartesian_product(...$inputs);

        $temp = [];
        foreach ($cartesian as $params) {
            $temp[] = $callback(...$params);
        }

        return new static($temp);
    }

    protected function sanitize(array $input): array
    {
        $data = [];
        $ignoreNullValues = !in_array('null', $this->types);

        foreach ($input as $key => $value) {
            if ($ignoreNullValues && $value === null) {
                continue;
            }

            is_of_the_type($value, ...$this->types) || throw InvalidElementType::make($value, $this->types);

            $newKey = $this instanceof MapInterface ? $this->normalizeKey($value, $key) : $key;
            $data[$newKey] = $value;
        }

        return $this instanceof VectorInterface ? array_values($data) : $data;
    }

}
