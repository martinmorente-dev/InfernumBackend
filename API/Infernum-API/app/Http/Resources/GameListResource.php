<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\ImageResource;
use App\Http\Resources\DiscountResource;
use App\Http\Resources\GenreResource;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GameListResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Dark Souls'),
        new OA\Property(property: 'short_description', type: 'string', example: 'is an acclaimed action role-playing game (ARPG) developed by FromSoftware'),
        new OA\Property(property: 'price', type: 'float', example: 39.99),
        new OA\Property(property: 'final_price', type: 'float', example: 20.99),
        new OA\Property(property: 'genres', ref: '#/components/schemas/GenreResource'),
        new OA\Property(property: 'images', ref: '#/components/schemas/ImageResource'),
        new OA\Property(property: 'discounts', ref: '#/components/schemas/DiscountResource')
    ]
)]
class GameListResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $discount = $this->discounts()->first();
        if ($discount && !($discount->valid_at <= now() && $discount->expires_at >= now()))
            $discount = null;
        $finalPrice = $discount ? $this->price - (($this->price * $discount->percentage) / 100) : $this->price;

        return [

            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'final_price' => round($finalPrice, 2),
            'genres' => GenreResource::collection(
                $this->whenLoaded('genres')
            ),
            'images' => ImageResource::collection(
                $this->whenLoaded('images')
            ),
            'discount' => $discount ? new DiscountResource($discount) : null,
        ];
    }
}
