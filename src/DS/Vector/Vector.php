<?php

declare(strict_types=1);

namespace PlanB\DS\Vector;

use PlanB\DS\Collection;
use PlanB\DS\Vector\Traits\VectorTrait;

/**
 * @template Key of int
 * @template Value
 * @template-extends Collection<Key, Value>
 */
class Vector extends Collection implements VectorImmutableInterface
{
    /**
     * @use VectorTrait<Key, Value>
     */
    use VectorTrait;
}
