<?php

declare(strict_types=1);

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'string', 'in:male,female'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'marital_status' => ['nullable', 'string', 'in:single,married,widowed,divorced'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'date_joined' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:visitor,new_convert,new_member,active,inactive,transferred,suspended,deceased'],
            'org_unit_id' => ['nullable', 'integer', 'exists:organizational_units,id'],
            'household_id' => ['nullable', 'integer', 'exists:households,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'spiritual_milestones' => ['nullable', 'array'],
        ];
    }
}
