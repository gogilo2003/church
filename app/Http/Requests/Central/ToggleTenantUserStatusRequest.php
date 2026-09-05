<?php

declare(strict_types=1);

namespace App\Http\Requests\Central;

use App\Http\Requests\Concerns\ValidatesInTenantContext;
use Illuminate\Foundation\Http\FormRequest;

class ToggleTenantUserStatusRequest extends FormRequest
{
    use ValidatesInTenantContext;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:active,suspended'],
        ];
    }
}
