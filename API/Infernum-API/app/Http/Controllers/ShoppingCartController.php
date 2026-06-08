<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShoppingCartRequest;
use App\Http\Resources\ShoppingCartResource;
use App\Models\CartItems;
use App\Models\Game;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

class ShoppingCartController extends Controller
{
    #[OA\Get(
        path: '/v1/cart/show',
        operationId: 'showShoppingCart',
        tags: ['Cart'],
        summary: 'Get shopping cart',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Shopping cart successfully retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'cart', ref: '#/components/schemas/ShoppingCartResource')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'The user does not have a shopping cart',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Failure: The user does not have a shopping cart')
                    ]
                )
            )
        ]
    )]
    public function show(): JsonResponse
    {
        $user = Auth::user();

        $cart = ShoppingCart::with(['cartItems.game.images'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart)
            return response()->json(['status' => 'Failure: The user does not have a shopping cart'], 404);

        $cart->load('cartItems.game.portraitImage');

        return response()->json([
            'status' => 'Succesfull',
            'cart' => ShoppingCartResource::make($cart)
        ]);
    }

    #[OA\Post(
        path: '/v1/cart/create',
        operationId: 'createCartItem',
        tags: ['Cart'],
        summary: 'Create shopping cart or add/update item',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateShoppingCartRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Item created or quantity successfully updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Succesfull'),
                        new OA\Property(property: 'message', type: 'string', enum: ['quantity updated', 'Cart and item created', 'item added']),
                        new OA\Property(property: 'cart_id', type: 'integer', example: 1),
                        new OA\Property(property: 'total_items', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'game_id',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'You need to send the game_id')
                                ),
                                new OA\Property(
                                    property: 'quantity',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'You need to introduce a quantity')
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found'
            )
        ]
    )]
    public function create(CreateShoppingCartRequest $request): JsonResponse
    {
        $user = Auth::user();

        $cart = ShoppingCart::firstOrCreate([
            'user_id' => $user->id
        ]);

        $item = CartItems::where('shopping_cart_id', $cart->id)->where('game_id', $request->game_id)->first();

        $game = Game::findOrFail($request->game_id);

        if ($item) {
            return response()->json([
                'status' => 'Successfull',
                'message' => 'You already added that game',
                'cart_id' => $cart->id,
                'total_items' => $cart->cartItems()->count()
            ], 200);
        }

        if ($user->games()->where('game_id', $request->game_id)->exists())
            return response()->json(['status' => 'Failure', 'message' => 'You already own this game in your library']);

        CartItems::create([
            'shopping_cart_id' => $cart->id,
            'game_id' => $request->game_id,
            'price' => $game->price
        ]);

        $action = $cart->wasRecentlyCreated ? 'Cart and item created' : 'item added';

        return response()->json([
            'status' => 'Successfull',
            'message' => $action,
            'cart_id' => $cart->id,
            'total_items' => $cart->cartItems()->count()
        ], 200);
    }

    public function cancel(): JsonResponse
    {
        $user = Auth::user();
        $cart = ShoppingCart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->cartItems()->delete();
            $cart->delete();
        }

        return response()->json(['status' => 'Success', 'message' => 'Cart cancelled and deleted']);
    }
}
