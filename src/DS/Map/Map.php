<?php

declare(strict_types=1);

namespace PlanB\DS\Map;

use PlanB\DS\Map\Traits\MapTrait;

class Map implements MapImmutableInterface
{
    use MapTrait;
    private readonly array $data;
}
