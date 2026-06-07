<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebHookController;

class WebHookController extends CashierWebHookController
{
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        $session = $payload['data']['object'];
        $userId = $session['metadata']['user_id'] ?? null;

        if ($userId)
        {
            $user = User::find($userId);
            if ($user && $user->shoppingCart)
            {
                $gameIds = $user->shoppingCart->cartItems->pluck('game_id')->toArray();

                $user->games()->syncWithoutDetaching($gameIds);

                $user->shoppingCart->cartItems()->delete();

                $user->shoppingCart->delete();
            }
        }
        return $this->successMethod();
    }
}
