<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerchantRequest extends FormRequest
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
            'merchant_name' => ['required','string'],
            'umkm_category_id' => ['required','string'],
            'address' => ['required','string'],
            'wa_number' => ['required','string'],
            'merchant_website_url' => ['string'],
            'logo' => ['required','image','mimes:jpeg,jpg,png,svg','max:2048'],
            'operational_time_oneday' => ['required','string'],
            'description' => ['required','string'],
        ];
    }
}
