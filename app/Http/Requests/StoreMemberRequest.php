<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => "required|string",
            "last_name" => "required|string",
            "email" => "nullable|string|email",
            "phone" => "nullable|string",
            "box_no" => "nullable|string",
            "post_code" => "nullable|string",
            "town" => "nullable|string",
            "address" => "nullable|string",
            "date_of_birth" => "required|date",
            "gender" => "required|boolean",
        ];
    }
}
