<?php

declare(strict_types=1);

namespace PlanB\DS;

use Countable;
use IteratorAggregate;
use PlanB\DS\Sequence\Sequence;

interface CollectionInterface extends Countable, IteratorAggregate /* , ArrayAccess, JsonSerializable*/
{
    public function __construct(iterable $input = []);

    public static function collect(iterable $input = []): static;

    public function getAllowedTypes(): array;

    public function isNotEmpty(): bool;

    public function isEmpty(): bool;

    public function toArray(): array;

    public function get(mixed $key, mixed $default = null): mixed;

    public function head(): mixed;

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

    public function any(callable $condition): bool;

    public function all(callable $condition): bool;

    public function each(callable $callback): static;

    public function filter(callable $condition = null): static;

    public function sort(callable $comparison = null): static;

    public function diff(iterable $input, callable $comparison = null): static;

    public function unique(): static;

    public function reversed(): static;

    public function reduce(callable $callback, mixed $initial = null): mixed;

    public function flatten(int $depth = PHP_INT_MAX): Sequence;

    public function flatMap(callable $callback): Sequence;

    public function collapse(int $depth = PHP_INT_MAX, string $glue = DIRECTORY_SEPARATOR): static;

    public function applyTo(callable $callback): mixed;
}
