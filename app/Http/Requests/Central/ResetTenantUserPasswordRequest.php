<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Http\Requests\Concerns\ValidatesInTenantContext;
use Illuminate\Foundation\Http\FormRequest;

class ResetTenantUserPasswordRequest extends FormRequest
{
    use ValidatesInTenantContext;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
