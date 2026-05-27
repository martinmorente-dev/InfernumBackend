<?php

namespace App\Http\Requests;

use App\Models\CartItems;
use App\Models\Game;
use App\Models\ShoppingCart;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateShoppingCartRequest',
    title: 'CreateShoppingCartRequest',
    description: 'Request to create or add item to shopping cart',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'game_id',
            type: 'integer',
            example: 1,
            description: 'Game ID (must exist in games table)'
        )
    ],
    required: ['game_id']
)]
class CreateShoppingCartRequest extends FormRequest
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
            'game_id' => 'required|exists:games,id'
        ];
    }

    public function messages(): array
    {
        return [
            'game_id.required' => 'You need to send the game_id',
            'game.exists' => 'The game need to exists first'
        ];
    }
}
