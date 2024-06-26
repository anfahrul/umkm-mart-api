<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMerchantRequest extends FormRequest
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
            'merchant_name' => ['string'],
            'umkm_category_id' => ['required','integer'],
            'address' => ['required','string'],
            'is_open' => ['required','boolean'],
            'wa_number' => ['required','string','regex:/^62\d+$/'],
            'merchant_website_url' => ['string'],
            'operational_time_oneday' => ['required','integer'],
            'logo' => ['image','mimes:jpeg,jpg,png,svg','max:2048'],
            'description' => ['required','string'],
        ];
    }
}
