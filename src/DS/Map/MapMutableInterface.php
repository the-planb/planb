<?php

declare(strict_types=1);

namespace PlanB\DS\Map;

interface MapMutableInterface extends MapInterface
{
    public function putAll(iterable $input): static;

    public function put(mixed $key, mixed $value): static;

    public function remove(mixed $key): static;

    public function removeValue(mixed $value): static;
}
