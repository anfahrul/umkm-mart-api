<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreProductRequest extends FormRequest
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
            'name' => ['required','string'],
            'product_category_id' => ['required','integer', 'min:1'],
            'minimal_order' => ['required','integer', 'min:1'],
            'short_desc' => ['required','string'],
            'price_value' => ['required','integer', 'min:1'],
            'stock_value' => ['required','integer', 'min:0'],
            'images' => ['required'],
            'images.*' => ['required','image','mimes:jpeg,jpg,png,svg','max:2048'],
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
