<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use OpenApi\Attributes as OA;

class LibraryResource extends JsonResource
{
    #[OA\Schema(
        schema: 'LibraryResource',
        type: 'object',
        properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'name', type: 'string', example: 'The Witcher 3'),
            new OA\Property(property: 'short_description', type: 'string', example: 'An open world RPG game'),
            new OA\Property(property: 'image', ref: '#/components/schemas/ImageResource')
        ]
    )]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'image' => $this->portraitImage ? new ImageResource($this->portrait) : null
        ];
    }
}
