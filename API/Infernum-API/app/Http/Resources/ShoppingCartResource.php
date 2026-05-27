<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CartItemsResource;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ShoppingCartResource',
    title: 'ShoppingCartResource',
    description: 'Shopping cart resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'items', ref: '#components/schemas/CartItemsResource')
    ],
    type: 'object'
)]
class ShoppingCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'items' => CartItemsResource::collection($this->cartItems)
        ];
    }
}
