<?php

declare(strict_types=1);

namespace PlanB\DS;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use PlanB\DS\Vector\Vector;
use PlanB\Type\ArrayValue;

interface CollectionInterface extends Countable, IteratorAggregate, ArrayValue, JsonSerializable
{
    public static function collect(iterable $input = [], ?callable $mapping = null): static;

    public function normalize(callable $callback): static;

    public function getAllowedTypes(): array;

    public function isNotEmpty(): bool;

    public function isEmpty(): bool;

    public function get(string|int $key, mixed $default = null): mixed;

    public function first(): mixed;

    public function firstThat(callable $condition): mixed;

    public function last(): mixed;

    public function lastThat(callable $condition): mixed;

    public function init(): static;

    public function tail(): static;

    public function take(int $numOfElements): static;

    public function drop(int $numOfElements): static;

    public function takeWhile(callable $condition): static;

    public function dropWhile(callable $condition): static;

    public function hasValue(mixed $value): bool;

    public function contains(mixed ...$values): bool;

    public function find(mixed $value): mixed;

    public function some(callable $condition): bool;

    public function every(callable $condition): bool;

    public function each(callable $callback): static;

    public function filter(?callable $condition = null): static;

    public function sort(?callable $comparison = null): static;

    public function diff(iterable $input, ?callable $comparison = null): static;

    public function unique(): static;

    public function reversed(): static;

    public function reduce(callable $callback, mixed $initial = null): mixed;

    public function flatten(int $depth = PHP_INT_MAX): Vector;

    public function flatMap(callable $callback): Vector;

    public function collapse(int $depth = PHP_INT_MAX, string $glue = DIRECTORY_SEPARATOR): static;

    public function shuffle(): static;

}
