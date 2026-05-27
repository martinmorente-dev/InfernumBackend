<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartItemsResource',
    title: 'CartItemsResource',
    description: 'Cart item resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Example game'),
        new OA\Property(property: 'short_description', type: 'string', example: 'Short description of the game'),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'https://example.com/image.jpg')
    ],
    type: 'object'
)]
class CartItemsResource extends JsonResource
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
            'name' => $this->game->name,
            'short_description' => $this->game->short_description,
            'image_url' => $this->game->portraitImage?->url
        ];
    }
}
