<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContributionTypeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "description" => "required|string",
            "recurrent" => "nullable|boolean",
            "recurrence_unit" => "nullable|required_if:recurrent,true|string|in:day,week,month,year",
            "recurrence_value" => "nullable|required_if:recurrent,true|integer",
            "deadline" => "nullable|date",
            "amount" => "nullable|numeric",
            "back_date" => "nullable|boolean",
            "autoenroll" => "nullable|boolean",
        ];
    }
}
