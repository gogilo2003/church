<?php

namespace App\Rules;

use Closure;
use App\Models\Member;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueParticipantCombination implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $values, Closure $fail): void
    {
        $member = Member::where('first_name', $values['first_name'])
            ->where('last_name', $values['last_name'])
            ->where('phone', $values['phone'])
            ->where('email', $values['email'])
            ->where('date_of_birth', $values['date_of_birth'])
            ->first();

        if ($member) {
            $fail('This member is already registered.');
        }
    }
}
