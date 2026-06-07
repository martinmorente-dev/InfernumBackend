<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebHookController;

class WebHookController extends CashierWebHookController
{
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        \Illuminate\Support\Facades\Log::info('Stripe Webhook received: checkout.session.completed');
        
        $session = $payload['data']['object'];
        $userId = $session['metadata']['user_id'] ?? null;
        
        \Illuminate\Support\Facades\Log::info('Webhook User ID: ' . ($userId ?? 'NULL'));

        if ($userId)
        {
            $user = User::find($userId);
            if ($user) {
                if ($user->shoppingCart) {
                    $gameIds = $user->shoppingCart->cartItems->pluck('game_id')->toArray();
                    \Illuminate\Support\Facades\Log::info('Syncing games to library: ' . json_encode($gameIds));

                    $user->games()->syncWithoutDetaching($gameIds);
                    $user->shoppingCart->cartItems()->delete();
                    $user->shoppingCart->delete();
                    
                    \Illuminate\Support\Facades\Log::info('Webhook library sync successful and cart deleted.');
                } else {
                    \Illuminate\Support\Facades\Log::warning('Stripe Webhook: User has no active shopping cart.');
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Stripe Webhook: User not found with ID ' . $userId);
            }
        }
        return $this->successMethod();
    }
}
