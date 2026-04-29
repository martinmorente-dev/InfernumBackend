<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProfileRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'display_name', type: 'string', example: 'manolo'),
        new OA\Property(property: 'bio', type: 'text', example: 'Yo solo soy un jugador'),
        new OA\Property(property: 'profile_picture', type: 'string', example: 'https://image.webp'),
    ],
    required: ['email', 'password']
)]
class ProfileRequest extends FormRequest
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
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:65535',
            'profile_picture' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp'
        ];
    }

    public function messages(): array
    {
        return [
            'display_name.string' => 'The name need to be just text',
            'display_name.max' => 'Text need to be shorter',
            'bio.string' => 'The bio need to be just text',
            'bio.max' => 'The bio need to be shorter',
            'profile_picture.string' => 'You need to introduce a url',
            'profile_picture.max' => 'The file is to big',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'Error',
            'errors' => $validator->errors()->toArray()
        ], 422));
    }
}
