<?php

declare(strict_types=1);

namespace PlanB\DS\Map;

use PlanB\DS\Map\Traits\MapTrait;

class MapMutable implements MapMutableInterface
{
    use MapTrait;

    private array $data;

    public function putAll(iterable $input): static
    {
        $this->assert($input);
        foreach ($input as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function put(mixed $key, mixed $value): static
    {
        $this->assert([$value]);
        $this->data[$key] = $value;

        return $this;
    }

    public function remove(mixed $key): static
    {
        unset($this->data[$key]);

        return $this;
    }

    public function removeValue(mixed $value): static
    {
        $key = $this->find($value);
        if (null === $key) {
            return $this;
        }

        return $this->remove($key);
    }
}
