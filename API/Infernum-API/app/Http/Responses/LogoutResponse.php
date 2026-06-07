<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $referer = $request->header('referer');
        if ($referer) {
            $parsed = parse_url($referer);
            $host = $parsed['host'] ?? 'localhost';
            
            if ($host === 'localhost' || $host === '127.0.0.1') {
                return redirect('http://localhost:4220/');
            }
        }
        
        $frontendUrl = env('FRONTEND_URL', 'https://frontend-infernum-original.duckdns.org');
        return redirect(rtrim($frontendUrl, '/') . '/');
    }
}
