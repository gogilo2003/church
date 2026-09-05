# Skill Playbook: Create FormRequest

## Request Template
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\DataTransferObjects\MemberData;
use App\Models\Tenant\Member;
use Illuminate\Foundation\Http\FormRequest;

final class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Member::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ];
    }

    public function toDTO(): MemberData
    {
        return new MemberData(
            firstName: $this->validated('first_name'),
            lastName: $this->validated('last_name'),
            email: $this->validated('email'),
            phone: $this->validated('phone'),
            gender: $this->validated('gender'),
            dateOfBirth: $this->validated('date_of_birth'),
        );
    }
}
```
