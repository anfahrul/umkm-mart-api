<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => ['required','string'],
            'phone_number' => ['required','string'],
            'state' => ['required','string'],
            'province' => ['required','string'],
            'city' => ['required','string'],
            'postal_code' => ['required','string'],
            'address' => ['required','string'],
        ];
    }
}
