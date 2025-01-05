<?php

declare(strict_types=1);

namespace PlanB\DS\Vector;

use PlanB\DS\Collection;
use PlanB\DS\Vector\Traits\VectorMutableTrait;

/**
 * @template Key of int
 * @template Value
 * @template-extends Collection<Key, Value>
 */
class VectorMutable extends Collection implements VectorMutableInterface
{
    /**
     * @use VectorMutableTrait<Key, Value>
     */
    use VectorMutableTrait;
}
