<?php

declare(strict_types=1);

namespace PlanB\DS\Sequence;

interface SequenceMutableInterface extends SequenceInterface
{
    public function addAll(iterable $input): static;

    public function add(mixed $value): static;

    public function insert(int $index, mixed ...$values): static;

    public function set(int $index, mixed $value): static;

    public function remove(int $index): static;

    public function removeValue(mixed $value): static;
}
