<?php

declare(strict_types=1);

namespace PlanB\DS\Sequence;

use PlanB\DS\Sequence\Traits\SequenceTrait;
use PlanB\DS\Traits\CollectionTrait;

class SequenceMutable implements SequenceMutableInterface
{
    use CollectionTrait;
    use SequenceTrait;

    private array $data = [];

    public function addAll(iterable $input): static
    {
        $this->assert($input);
        foreach ($input as $value) {
            $this->data[] = $value;
        }

        return $this;
    }

    public function add(mixed $value): static
    {
        $this->assert([$value]);
        $this->data[] = $value;

        return $this;
    }

    public function insert(int $index, mixed ...$values): static
    {
        $this->assert($values);
        array_splice($this->data, $index, 0, $values);

        return $this;
    }

    public function set(int $index, mixed $value): static
    {
        array_splice($this->data, $index, 1, $value);

        return $this;
    }

    public function removeValue(mixed $value): static
    {
        $index = $this->find($value);

        if (null === $index) {
            return $this;
        }

        return $this->remove($index);
    }

    public function remove(int $index): static
    {
        array_splice($this->data, $index, 1);

        return $this;
    }
}
