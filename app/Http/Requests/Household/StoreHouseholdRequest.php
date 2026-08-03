<?php

declare(strict_types=1);

namespace App\Http\Requests\Household;

use Illuminate\Foundation\Http\FormRequest;

final class StoreHouseholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'org_unit_id' => ['nullable', 'integer', 'exists:organizational_units,id'],
            'primary_contact_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'marriage_date' => ['nullable', 'date'],
            'member_relationships' => ['nullable', 'array'],
            'member_relationships.*.member_id' => ['required', 'integer', 'exists:members,id'],
            'member_relationships.*.relationship' => ['required', 'string', 'in:head,spouse,child,parent,guardian,other'],
        ];
    }
}
