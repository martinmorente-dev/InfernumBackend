<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Resources\GameResource;
use App\Http\Resources\GameListResource;
use App\Filters\GameFilter;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use OpenApi\Attributes as OA;

class GameController extends Controller
{

    #[OA\Get(
        path: '/v1/games/details/{id}',
        operationId: 'details',
        tags: ['Game'],
        summary: 'Get game details',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Game retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'game', ref: '#/components/schemas/GameResource')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found'
            )
        ]
    )]
    public function details(int $id): JsonResponse
    {
        $game = Game::with(['genres', 'images', 'requirements', 'discounts'])->find($id);

        if (!$game)
            return response()->json(['status' => 'Error: Game not found'], 404);

        return response()->json([
            'status' => 'Succesfull',
            'game' => new GameResource($game)
        ], 200);
    }

    #[OA\Get(
        path: '/v1/games/all/{pagination}',
        operationId: 'all',
        tags: ['Game'],
        summary: 'Get all games',
        parameters: [
            new OA\Parameter(
                name: 'pagination',
                in: 'path',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Games retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'game', ref: '#/components/schemas/GameListResource'),
                        new OA\Property(
                            property: 'pagination',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'total', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                                new OA\Property(property: 'from', type: 'integer', example: 1),
                                new OA\Property(property: 'to', type: 'integer', example: 1),
                                new OA\Property(property: 'has_next_page', type: 'boolean', example: false),
                                new OA\Property(property: 'next_page', type: 'string', example: 'http://next_page'),
                                new OA\Property(property: 'previous_page', type: 'string', example: 'http://previous_page')
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function all(int $pagination = 10): JsonResponse
    {
        $games = Game::with(['genres', 'images', 'discounts'])->paginate($pagination);

        return response()->json([
            'status' => 'Succesfull',
            'game' => GameListResource::collection($games),
            'pagination' => [
                'current_page' => $games->currentPage(),
                'total' => $games->total(),
                'per_page' => $games->perPage(),
                'last_page' => $games->lastPage(),
                'from' => $games->firstItem(),
                'to' => $games->lastItem(),
                'has_more_page' => $games->hasMorePages(),
                'next_page' => $games->nextPageUrl(),
                'previous_page' => $games->previousPageUrl()
            ]
        ]);
    }

    #[OA\Get(
        path: '/v1/games/filter',
        operationId: 'filterGame',
        tags: ['Game'],
        summary: 'Filter games',
        parameters: [
            new OA\Parameter(
                name: 'price[gt]',
                in: 'query',
                required: false,
                description: 'Minimum price (greater than)',
                schema: new OA\Schema(type: 'number', example: 10)
            ),
            new OA\Parameter(
                name: 'genre',
                in: 'query',
                required: false,
                description: 'Game matching the genre',
                schema: new OA\Schema(type: 'string', example: 'RPG')
            ),
            new OA\Parameter(
                name: 'name',
                in: 'query',
                required: false,
                description: 'Game with a name similar to the query',
                schema: new OA\Schema(type: 'string', example: 'Dark')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Games retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'game', ref: '#/components/schemas/GameListResource'),
                        new OA\Property(
                            property: 'pagination',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'total', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                                new OA\Property(property: 'from', type: 'integer', example: 1),
                                new OA\Property(property: 'to', type: 'integer', example: 1),
                                new OA\Property(property: 'has_next_page', type: 'boolean', example: false),
                                new OA\Property(property: 'next_page', type: 'string', example: 'http://next_page'),
                                new OA\Property(property: 'previous_page', type: 'string', example: 'http://previous_page')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found by the filter given'
            )
        ]
    )]
    public function filterGame(Request $request): JsonResponse
    {
        $filter = new GameFilter();
        $queryItems = $filter->transform($request);
        $query = Game::with(['discounts']);

        if (!empty($queryItems))
            $query->where($queryItems);

        if ($request->filled('genre'))
            $query->gameByGenre($request->genre);

        $games = $query->paginate()->appends(request()->query());

        if ($games->isEmpty())
            return response()->json(['status' => 'Failure: Game not found by the filter given'], 404);

        return response()->json([
            'status' => 'Succesful',
            'games' => GameListResource::collection($games),
            'pagination' => [
                'current_page' => $games->currentPage(),
                'total' => $games->total(),
                'per_page' => $games->perPage(),
                'last_page' => $games->lastPage(),
                'from' => $games->firstItem(),
                'to' => $games->lastItem(),
                'has_more_page' => $games->hasMorePages(),
                'next_page' => $games->nextPageUrl(),
                'previous_page' => $games->previousPageUrl()
            ]
        ]);
    }

    #[OA\Get(
        path: '/v1/games/most-bought',
        operationId: 'gameMostBought',
        tags: ['Game'],
        summary: 'Most purchased game',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Game retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'game', ref: '#/components/schemas/GameListResource'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Games were not bought yet'
            )
        ]
    )]
    public function gameMostBought(): JsonResponse
    {
        $game = Game::with(['images', 'discounts'])
            ->where('count_boughts', '>', 0)
            ->orderBy('count_boughts', 'desc')
            ->first();

        if (!$game)
            return response()->json(['Failure' => 'Games were not bought yet', 404]);

        return response()->json([
            'status' => 'Succesful',
            'game' => new GameListResource($game)
        ]);
    }
}
