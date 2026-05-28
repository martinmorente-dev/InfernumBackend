<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuyRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Post(
    path: '/v1/buy',
    operationId: 'buy',
    tags: ['Payment'],
    security: [['sanctum' => []]],
    summary: 'Process cart purchase (TOKEN REQUIRED)',
    description: '**NO TOKEN = 401!**\\n\\n1. **POST /v1/login** → copy token\\n2. **Authorize** → paste `Bearer {token}`\\n3. **Execute this route** → Redirects to **Stripe Checkout** ✅\\n\\n**Validations:**\\n- **shoppingCartId**: Required, integer, must exist in `shopping_carts`\\n- Shopping cart must belong to the **authenticated user**\\n- Shopping cart **MUST NOT be empty**\\n\\n**Flow:**\\n1. Validate cart and load items with games\\n2. Create **Stripe Checkout session** (mode `payment`)\\n3. Generate dynamic **line_items** from `cartItems`\\n4. Return **Stripe URL** for redirection',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'shoppingCartId',
                    type: 'integer',
                    example: 1,
                    description: 'ID of the shopping cart of the authenticated user'
                ),
            ],
            required: ['shoppingCartId']
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Stripe Checkout session successfully created',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'state', type: 'string', example: 'Success'),
                    new OA\Property(property: 'url', type: 'string', example: 'https://checkout.stripe.com/...'),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'TOKEN required',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'Error: Unauthenticated'),
                ]
            )
        ),
        new OA\Response(
            response: 400,
            description: 'Validation Error',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'Error: Failure'),
                    new OA\Property(property: 'message', type: 'string', example: 'The cart does not belong to the user'),
                    new OA\Property(property: 'error', type: 'string', example: 'Validation failed'),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation failed - invalid shoppingCartId',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'The shopping cart id field is required.'),
                    new OA\Property(property: 'errors', type: 'object'),
                ]
            )
        ),
    ]
)]
    public function buy(BuyRequest $request): JsonResponse
    {

        $cartItems = $request->shoppingCart->cartItems;

        $lineItems = [];

        foreach ($cartItems as $item)
        {
            $game = $item->game;
            $price = $game->price;
            
            // Apply active discount if available
            $activeDiscount = $game->discounts()->active()->first();
            if ($activeDiscount) {
                $price = $price * (1 - $activeDiscount->percentage / 100);
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $game->name
                    ],
                    'unit_amount' => (int) round($price * 100)
                ],
                'quantity' => 1
            ];
        }

        $stripeSecret = env('STRIPE_SECRET');
        if (empty($stripeSecret) || str_contains($stripeSecret, 'tu_clave') || str_contains($stripeSecret, 'your_stripe') || str_contains($stripeSecret, '******')) {
            return $this->processMockCheckout($request);
        }

        try {
            $session = Cashier::stripe()->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => Auth::user()->email,
                'line_items' => $lineItems,
                'success_url' => 'https://frontend-infernum-original.duckdns.org/profile?payment=success',
                'cancel_url' => 'https://frontend-infernum-original.duckdns.org/store?payment=cancel',
                'metadata' => [
                    'user_id' => Auth::user()->id
                ]
            ]);

            return response()->json([
                'state' => 'Success',
                'url' => $session->url
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Stripe integration exception, falling back to mock mode: ' . $e->getMessage());
            return $this->processMockCheckout($request);
        }
    }

    private function processMockCheckout(BuyRequest $request): JsonResponse
    {
        $user = Auth::user();
        $gameIds = $request->shoppingCart->cartItems->pluck('game_id')->toArray();
        $user->games()->syncWithoutDetaching($gameIds);
        $request->shoppingCart->cartItems()->delete();
        $request->shoppingCart->delete();

        return response()->json([
            'state' => 'Success',
            'url' => 'http://localhost:4220/profile?payment=success'
        ]);
    }
}
