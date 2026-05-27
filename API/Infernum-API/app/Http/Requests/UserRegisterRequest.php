<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserRegisterRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
        new OA\Property(property: 'nickname', type: 'string', example: 'john_doe'),
        new OA\Property(property: 'password', type: 'string', example: 'password123'),
    ],
    required: ['email', 'nickname', 'password']
)]
class UserRegisterRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'nickname' => 'required|unique:users,nickname',
            'password' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'You must specify an email address',
            'email.email' => 'You must enter a valid email address',
            'email.unique' => 'This email is already registered',
            'nickname.required' => 'You must specify a nickname',
            'nickname.unique' => 'This nickname is already taken',
            'password.required' => 'You must enter a password'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'Error',
            'message' => 'Bad data',
            'errors' => $validator->errors()->toArray()
        ], 422));
    }
}
