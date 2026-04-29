<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: 'UserProfileResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
        new OA\Property(property: 'profile', ref: '#/components/schemas/ProfileResource')
    ]
)]
class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            'user' => UserResource::make($this->resource),
            'profile' => ProfileResource::make($this->profile)
        ];
    }
}
