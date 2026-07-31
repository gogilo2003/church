<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class KenyaE164Phone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^\+254\d{9}$/', $value)) {
            $fail('The :attribute must be a valid Kenyan phone number in E.164 format. e.g. +254711xxxxxx');
        }
    }
}
