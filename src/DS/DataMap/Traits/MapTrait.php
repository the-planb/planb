<?php

declare(strict_types=1);

namespace PlanB\DS\DataMap\Traits;

use Ds\Map;
use PlanB\DS\DataMap\DataList;
use PlanB\DS\DataMap\DataMap;

trait MapTrait
{
    use SequenceTrait;

    private Map $data;

    final public function __construct(iterable $input = [], string ...$types)
    {
        $this->types = 0 < count($types) ? $types : null;

        $this->assert($input);
        $this->data   = new Map($input);
        $this->length = $this->data->count();
    }


    public function putAll(iterable $input): static
    {
        $data = $this->data->copy();
        $data->putAll($input);

        return $this->copy($data);
    }


    public function put(mixed $key, mixed $value): static
    {
        $this->assert([$value]);

        $data = $this->data->copy();
        $data->put($key, $value);

        return $this->copy($data);
    }

    public function hasKey(mixed $key): bool
    {
        return $this->data->hasKey($key);
    }

    public function hasValue(mixed $key): bool
    {
        return $this->data->hasValue($key);
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        if (1 === func_num_args()) {
            return $this->data->get($key);
        }

        return $this->data->get($key, $default);
    }

    public function remove(mixed $key): static
    {
        $data = $this->data->copy();
        $data->remove($key);

        return $this->copy($data);
    }

    public function values(): DataList
    {
        $data = $this->data->values();

        return DataList::collect($data, ...$this->types);
    }

    public function keys(): DataList
    {
        $data = $this->data->keys();

        return DataList::collect($data);
    }

    public function map(callable $callback): self
    {
        $data  = $this->data->copy();
        $input = $data->map($callback);

        return DataMap::mixed($input);
    }

    public function ksort(callable $callback = null): static
    {
        $callback ??= static fn ($first, $second) => $first <=> $second;
        $data     = $this->data->ksorted($callback);

        return $this->copy($data);
    }

    public function diff(iterable $input): static
    {
        $map  = new Map($input);
        $data = $this->data->diff($map);

        return $this->copy($data);
    }

    public function intersect(iterable $input): static
    {
        $map  = new Map($input);
        $data = $this->data->intersect($map);

        return $this->copy($data);
    }
}
