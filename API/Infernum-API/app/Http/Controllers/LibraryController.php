<?php

namespace App\Http\Controllers;

use App\Http\Resources\LibraryResource;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class LibraryController extends Controller
{

    #[OA\Get(
        path: '/v1/library/games',
        operationId: 'Show Library',
        tags: ['Library'],
        summary: 'Get library',
        description: '⚠️ Requires authentication. Include the Bearer token in the header: `Authorization: Bearer {token}`',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Library retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'message', type: 'string', example: 'Library is empty', nullable: true),
                        new OA\Property(
                            property: 'games',
                            type: 'array',
                            nullable: true,
                            items: new OA\Items(ref: '#/components/schemas/LibraryResource')
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated - You must include the Bearer token in the header: Authorization: Bearer {token}',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')
                    ]
                )
            )
        ]
    )]
    public function show()
    {
        $games = Auth::user()->games()
            ->with('portraitImage')
            ->paginate(10);
 
        if ($games->isEmpty())
            return response()->json(['status' => 'Succesfull', 'message' => 'Library is empty'], 200);

        return response()->json([
            'status' => 'Succesfull',
            'games' => LibraryResource::collection($games)
        ]);
    }
}
