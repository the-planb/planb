<?php

declare(strict_types=1);

namespace PlanB\DS\Map;

use PlanB\DS\CollectionInterface;
use PlanB\DS\Sequence\Sequence;

interface MapInterface extends CollectionInterface
{
    // INFO
    public function hasKey(mixed $key): bool;

    public function values(): Sequence;

    public function keys(): Sequence;


    // MODIFICATION
    public function normalizeKey(mixed $value, mixed $key): mixed;

    public function merge(iterable $input): static;

    public function map(callable $callback): Map;

    public function mapKeys(callable $callback): static;

    public function ksort(callable $comparison = null): static;

    public function diffKeys(iterable $input, callable $comparison = null): static;

    public function intersect(iterable $input, callable $comparison = null): static;

    public function intersectKeys(iterable $input, callable $comparison = null): static;
}
