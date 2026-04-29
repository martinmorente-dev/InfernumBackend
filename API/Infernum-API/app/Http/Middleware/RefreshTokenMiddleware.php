<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user)
            $token = $user->currentAccessToken();
        if ($token->expires_at && $token->expires_at->isPast())
        {
            $token->delete();
            return response()->json(['status' => 'Token expired'], 401);
        }
        $token->expires_at = now()->addHour(3);
        $token->save();
        return $next($request);
    }
}
