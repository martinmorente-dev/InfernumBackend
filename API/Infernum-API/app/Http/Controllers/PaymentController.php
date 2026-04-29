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
    summary: 'Procesar compra del carrito (TOKEN OBLIGATORIO)',
    description: '**¡SIN TOKEN = 401!**\\n\\n1. **POST /v1/login** → copia token\\n2. **Authorize** → pega `Bearer {token}` en el candado\\n3. **Ejecuta esta ruta** → Redirige a **Stripe Checkout** ✅\\n\\n**Validaciones:**\\n- **shoppingCartId**: Obligatorio, entero, debe existir en `shopping_carts`\\n- Carrito debe pertenecer al **usuario autenticado**\\n- Carrito **NO debe estar vacío**\\n\\n**Flujo:**\\n1. Valida carrito y carga items con juegos\\n2. Crea **sesión Stripe Checkout** (modo `payment`)\\n3. Genera **line_items** dinámicos desde `cartItems`\\n4. Retorna **URL de Stripe** para redirección',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'shoppingCartId',
                    type: 'integer',
                    example: 1,
                    description: 'ID del carrito de compras del usuario autenticado'
                ),
            ],
            required: ['shoppingCartId']
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Sesión Stripe Checkout creada exitosamente',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'state', type: 'string', example: 'Success'),
                    new OA\Property(property: 'url', type: 'string', example: 'https://checkout.stripe.com/...'),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'TOKEN requerido',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'Error: No autenticado'),
                ]
            )
        ),
        new OA\Response(
            response: 400,
            description: 'Error de validación',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'Error: Failure'),
                    new OA\Property(property: 'message', type: 'string', example: 'El carrito no pertenece al usuario'),
                    new OA\Property(property: 'error', type: 'string', example: 'Validation failed'),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validación fallida - shoppingCartId inválido',
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
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->game->name
                    ],
                    'unit_amount' => $item->game->price * 100
                ],
                'quantity' => 1
            ];
        }

        $session = Cashier::stripe()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => 'https://httpstat.us/200',
            'cancel_url' => 'https://httpstat.us/400',
            'metadata' => [
                'user_id' => Auth::user()->id
            ]
        ]);

        return response()->json([
            'state' => 'Success',
            'url' => $session->url
        ]);
    }
}
