<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoSpecialCharacters implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/[^A-Za-z0-9\s]/', $value)) {
            $fail('The :attribute must not contain special characters.');
        }
    }
}
