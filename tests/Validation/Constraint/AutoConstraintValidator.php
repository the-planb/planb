<?php

declare(strict_types=1);

namespace PlanB\Tests\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AutoConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
    }
}
