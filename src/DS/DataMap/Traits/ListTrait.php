<?php

declare(strict_types=1);

namespace PlanB\DS\DataMap\Traits;

use Ds\Vector;
use PlanB\DS\DataMap\DataList;

trait ListTrait
{
    use SequenceTrait;

    private Vector $data;

    final public function __construct(iterable $input = [], string ...$types)
    {
        $this->types = 0 < count($types) ? $types : null;
        $this->assert($input);
        $this->data   = new Vector($input);
        $this->length = $this->data->count();
    }

    public static function range(mixed $start, mixed $end, int|float $step = 1): static
    {
        return new self(range($start, $end, $step));
    }

    public function add(mixed $value): static
    {
        return $this->addAll([$value]);
    }

    public function addAll(iterable $input): static
    {
        $values = array_values((array)$input);
        $data   = $this->data->copy();
        $data->push(...$values);

        return $this->copy($data);
    }

    public function hasIndex(int $index): bool
    {
        return $index < $this->count();
    }

    public function hasValue(mixed $value): bool
    {
        return $this->data->contains($value);
    }

    public function contains(mixed ...$values): bool
    {
        return $this->data->contains(...$values);
    }

    public function get(int $index, mixed $default = null): mixed
    {
        if (1 === func_num_args()) {
            return $this->data->get($index);
        }

        if (isset($this->data[$index])) {
            $this->data->get($index);
        }

        return $default;
    }

    public function remove(int $index): static
    {
        $data = $this->data->copy();
        $data->remove($index);

        return $this->copy($data);
    }

    public function map(callable $callback): self
    {
        $data  = $this->data->copy();
        $input = $data->map($callback);

        return DataList::mixed($input);
    }
}
