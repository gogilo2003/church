<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Http\Requests\Concerns\ValidatesInTenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreTenantUserRequest extends FormRequest
{
    use ValidatesInTenantContext;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:active,suspended'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ];
    }
}
