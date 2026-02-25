<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NotZero extends Constraint
{
    public string $message = 'This value cannot be zero.';

    // Link this constraint to its validator
    public function validatedBy(): string
    {
        return NotZeroValidator::class;
    }
}