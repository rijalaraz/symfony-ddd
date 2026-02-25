<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotZeroValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /**
         * @var NotZero $constraint
         */
        if ($value === 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}