<?php

declare(strict_types=1);

namespace PlanB\DS\Sequence\Traits;

use PlanB\DS\Map\Map;
use PlanB\DS\Sequence\Sequence;

trait SequenceTrait
{
    public function map(callable $callback): Sequence
    {
        $input = [];
        foreach ($this as $key => $value) {
            $input[$key] = $callback($value, $key);
        }

        return new Sequence($input);
    }

    public function hasIndex(int $index): bool
    {
        return array_key_exists($index, $this->data);
    }

    public function toMap(callable $callback): Map
    {
        return Map::collect($this->data)
            ->mapKeys($callback);
    }

    private function ensureData(array $input): array
    {
        return array_values($input);
    }
}
