<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductRequest extends FormRequest
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
            'name' => ['string'],
            'product_category_id' => ['integer', 'min:1'],
            'minimal_order' => ['string', 'min:1'],
            'short_desc' => ['string'],
            'price_value' => ['integer', 'min:1'],
            'stock_value' => ['integer', 'min:0'],
            'images.*' => ['image','mimes:jpeg,jpg,png,svg','max:2048'],
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
