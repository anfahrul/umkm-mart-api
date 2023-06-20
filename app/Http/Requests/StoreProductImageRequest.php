<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreProductImageRequest extends FormRequest
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
