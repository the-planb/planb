<?php

namespace PlanB\Text;

use PlanB\DS\Attribute\ElementType;
use PlanB\DS\Collection;
use PlanB\DS\Vector\Traits\VectorMutableTrait;
use PlanB\DS\Vector\VectorMutableInterface;

/**
 * @template-extends Collection<int, \Stringable::class>
 */
#[ElementType('string', \Stringable::class)]
final class TextList extends Collection implements VectorMutableInterface
{
    /**
     * @use VectorMutableTrait<int, \Stringable::class>
     */
    use VectorMutableTrait;

    public function implode(string $separator = ' '): string
    {
        return implode($separator, $this->toArray());
    }


}
