<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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
            'merchant_name' => ['required', 'string'],
            'umkm_category_id' => ['required','integer'],
            'address' => ['required','string'],
            'wa_number' => ['required','string','regex:/^62\d+$/'],
            'merchant_website_url' => ['string'],
            'logo' => ['mimes:jpeg,jpg,png,svg','max:2048'],
            'operational_time_oneday' => ['required','integer'],
            'description' => ['required','string'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            response()->json([
                'status'=> Response::HTTP_UNPROCESSABLE_ENTITY  . " Unprocessable Content",
                'message' => "Your request failed to process",
                'errors' => $errors,
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
