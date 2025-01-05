<?php

declare(strict_types=1);

namespace PlanB\DS\Map;

use PlanB\DS\Collection;
use PlanB\DS\Map\Traits\MapTrait;

/**
 *
 * @template Key of string|int
 * @template Value
 * @template-extends Collection<Key, Value>
 */
class Map extends Collection implements MapImmutableInterface
{
    /**
     * @use MapTrait<Key, Value>
     */
    use MapTrait;
}
