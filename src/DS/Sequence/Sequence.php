<?php

declare(strict_types=1);

namespace PlanB\DS\Sequence;

use PlanB\DS\Sequence\Traits\SequenceTrait;
use PlanB\DS\Traits\CollectionTrait;

class Sequence implements SequenceImmutableInterface
{
    use CollectionTrait;
    use SequenceTrait;

    private readonly array $data;
}
