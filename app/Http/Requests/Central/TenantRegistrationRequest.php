<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\DataTransferObjects\OnboardingData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class TenantRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'church_name' => ['required', 'string', 'max:150'],
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9]+$/',
                'not_in:admin,app,www,api,central,billing,support,mail,system',
                Rule::unique('domains', 'domain'),
            ],
            'admin_name' => ['required', 'string', 'max:100'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.regex' => 'The subdomain must contain only lowercase letters and numbers without spaces or special characters.',
            'subdomain.not_in' => 'This subdomain is reserved. Please choose another one.',
            'subdomain.unique' => 'This subdomain is already taken. Please choose another one.',
        ];
    }

    public function toDTO(): OnboardingData
    {
        return new OnboardingData(
            churchName: $this->validated('church_name'),
            subdomain: $this->validated('subdomain'),
            adminName: $this->validated('admin_name'),
            adminEmail: $this->validated('admin_email'),
            adminPassword: $this->validated('admin_password'),
            phone: $this->validated('phone'),
            isCentralAdminCreated: false,
        );
    }
}
