<?php

declare(strict_types=1);

namespace PlanB\Validation\Traits;

use PlanB\Validation\Traits\Exception\ConstraintNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;

trait ValidableTrait
{
    public static function isValid(mixed ...$input): bool
    {
        $callback = Validation::createIsValidCallable(...);

        return static::callValidator($callback, ...$input);
    }

    private static function callValidator(callable $callback, mixed ...$input)
    {
        $constraints = static::getConstraints();
        if ($constraints instanceof Constraint) {
            $constraints = [$constraints];
        }
        $validator = $callback(...$constraints);

        if (1 === count($input)) {
            return $validator(...array_values($input));
        }

        return $validator($input);
    }

    /**
     * @return Constraint[]|Constraint
     */
    public static function getConstraints(): array|Constraint
    {
        $pieces = explode('\\', __CLASS__);
        $last = array_pop($pieces);

        //al mismo nivel
        $pieces[] = 'Constraint';
        $pieces[] = $last;
        $constraintName = implode('\\', $pieces);

        if (class_exists($constraintName)) {
            return new $constraintName();
        }

        throw  ConstraintNotFoundException::make(__CLASS__, $constraintName);
    }

    public function assert(mixed ...$input): void
    {
        $callback = Validation::createCallable(...);

        static::callValidator($callback, ...$input);
    }
}
