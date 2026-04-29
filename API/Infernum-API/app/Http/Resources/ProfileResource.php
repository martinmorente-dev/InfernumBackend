<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class ProfileResource extends JsonResource
{

    #[OA\Schema(
        schema: 'ProfileResource',
        type: 'object',
        properties: [
            new OA\Property(property: 'id', type: 'integer', example: '1'),
            new OA\Property(property: 'display_name', type: 'string', example: 'example_user'),
            new OA\Property(property: 'bio', type: 'string', example: 'I am a normal user'),
            new OA\Property(property: 'profile_picture', type: 'string', example: 'https://png.webp')
        ]
    )]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'bio' => $this->bio ?? '',
            'profile_picture' => $this->profile_picture
        ];
    }
}
