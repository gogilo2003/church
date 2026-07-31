<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferingRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric', 'integer', 'exists:offerings,id'],
            'type' => ['required', 'numeric', 'integer', 'exists:offering_types,id'],
            'offering_date' => ['required', 'date', 'unique:offerings,offering_date,'.$this->id.',id'],
            'amount' => ['required', 'numeric'],
        ];
    }
}
