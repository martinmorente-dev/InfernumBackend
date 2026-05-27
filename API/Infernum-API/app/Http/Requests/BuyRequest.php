<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BuyRequest',
    title: 'BuyRequest',
    description: 'Request to process shopping cart purchase',
    properties: [
        new OA\Property(property: 'shoppingCartId', type: 'integer', example: 1),
    ],
    type: 'object',
    required: ['shoppingCartId']
)]
class BuyRequest extends FormRequest
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
            'shoppingCartId' => 'required|integer|exists:shopping_carts,id'
        ];
    }

    protected function passedValidation(): void
    {

        $shoppingCart = $this->user()
            ->shoppingCart()
            ->with('cartItems.game')
            ->where('id', $this->shoppingCartId)
            ->first();

        if (!$shoppingCart)
                throw ValidationException::withMessages(['shoppingCartId' => 'The cart does not belong to the user']);

        if ($shoppingCart->cartItems->isEmpty())
            throw ValidationException::withMessages(['shoppingCartId' => 'The cart has no items.']);

        $this->shoppingCart = $shoppingCart;
    }
}
