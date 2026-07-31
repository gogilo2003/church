<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AddCustomDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'domain' => [
                'required',
                'string',
                'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i',
                Rule::unique('domains', 'domain'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'domain.regex' => 'Please enter a valid domain format (e.g. mis.elck.org or churchabc.com).',
            'domain.unique' => 'This domain is already registered.',
        ];
    }
}
