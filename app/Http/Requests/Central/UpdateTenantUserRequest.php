<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Http\Requests\Concerns\ValidatesInTenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantUserRequest extends FormRequest
{
    use ValidatesInTenantContext;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:active,suspended'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ];
    }
}
