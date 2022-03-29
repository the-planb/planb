<?php

declare(strict_types=1);

namespace PlanB\Tests\DS\Traits;

use DateTime;
use PlanB\DS\Attribute\ElementType;
use PlanB\DS\CollectionInterface;
use PlanB\DS\Map\Map;
use PlanB\DS\Map\MapInterface;
use PlanB\DS\Map\MapMutable;
use PlanB\DS\Sequence\Sequence;
use PlanB\DS\Sequence\SequenceInterface;
use PlanB\DS\Sequence\SequenceMutable;
use PlanB\DS\Traits\CollectionTrait;
use Prophecy\PhpUnit\ProphecyTrait;

trait  ObjectMother
{
    use ProphecyTrait;

    private function give_me_a_typed_collection(iterable $input = []): CollectionInterface
    {
        return new #[ElementType('string', DateTime::class)] class($input) implements CollectionInterface {
            use CollectionTrait;

            private function ensureData(array $input): array
            {
                return $input;
            }
        };
    }

    private function give_me_an_empty_collection(): CollectionInterface
    {
        return $this->give_me_a_collection([]);
    }

    private function give_me_a_collection(?iterable $input = null): CollectionInterface
    {
        $input = is_null($input) ? $this->give_me_an_array() : $input;

        return new  class($input) implements CollectionInterface {
            use CollectionTrait;

            private function ensureData(array $input): array
            {
                return $input;
            }
        };
    }

    private function give_me_an_array(): array
    {
        return [
            'A' => 'value/A',
            'B' => 'value/B',
            'C' => 'value/C',
            'D' => 'value/D',
        ];
    }

    private function give_me_a_sequence(?iterable $input = null): SequenceInterface
    {
        $input = is_null($input) ? $this->give_me_an_array() : $input;

        return Sequence::collect($input);
    }

    private function give_me_a_map(?iterable $input = null): MapInterface
    {
        $input = is_null($input) ? $this->give_me_an_array() : $input;

        return Map::collect($input);
    }

    private function give_me_an_empty_map(): MapInterface
    {
        return Map::collect([]);
    }

    private function give_me_a_mutable_and_typed_map()
    {
        return new #[ElementType('string')] class extends MapMutable {

        };
    }

    private function give_me_a_mutable_and_typed_sequence()
    {
        return new #[ElementType('string')] class extends SequenceMutable {

        };
    }

}