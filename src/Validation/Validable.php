<?php

declare(strict_types=1);

namespace PlanB\Validation;

use Symfony\Component\Validator\Constraint;

interface Validable
{
    /**
     * @return Constraint[]|Constraint
     */
    public static function getConstraints(): array|Constraint;

    public static function isValid(mixed ...$input): bool;

    public function assert(mixed ...$input): void;
}
