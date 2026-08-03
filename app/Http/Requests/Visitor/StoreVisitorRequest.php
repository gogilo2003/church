<?php

declare(strict_types=1);

namespace App\Http\Requests\Visitor;

use Illuminate\Foundation\Http\FormRequest;

final class StoreVisitorRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'org_unit_id' => ['nullable', 'integer', 'exists:organizational_units,id'],
            'visit_date' => ['nullable', 'date'],
            'visit_purpose' => ['nullable', 'string', 'max:255'],
            'prayer_requests' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
