<?php

declare(strict_types=1);

namespace PlanB\DS\Sequence;

use PlanB\DS\CollectionInterface;
use PlanB\DS\Map\MapInterface;

interface SequenceInterface extends CollectionInterface
{
    public function hasIndex(int $index): bool;

    public function map(callable $callback): Sequence;

    public function toMap(callable $callback): MapInterface;
}
