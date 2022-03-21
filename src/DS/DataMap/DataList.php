<?php

declare(strict_types=1);

namespace PlanB\DS\DataMap;

use IteratorAggregate;
use PlanB\DS\DataMap\Traits\ListTrait;

final class DataList implements IteratorAggregate
{
    use ListTrait;

    public static function mixed(iterable $input): self
    {
        return new self($input, 'string', 'int', 'float', 'bool', 'null', 'array', 'object', 'callable', 'resource');
    }
}
