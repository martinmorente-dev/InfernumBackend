<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: 'ImageResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'url', type: 'string', example: 'https://image.com'),
        new OA\Property(property: 'type', type: 'enum', example: 'portrait')
    ]
)]
class ImageResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $url = $this->url;

        // If the url is a relative storage path (uploaded via Filament), resolve it.
        // External URLs (http/https) are returned as-is.
        if ($url && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            if (str_starts_with($url, 'storage/')) {
                $url = str_replace('storage/', '', $url);
            }
            $url = Storage::disk('public')->url($url);
        }

        if ($url && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . ltrim($url, '/');
        }

        return [
            'id'   => $this->id,
            'url'  => $url,
            'type' => $this->type,
        ];
    }
}
