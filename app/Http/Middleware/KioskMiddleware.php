<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KioskMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $uuid = session('kiosk_uuid');

        if (!$uuid) {
            abort(403, 'No kiosk session');
        }

        $kiosk = \App\Models\KioskSession::where('uuid', $uuid)->first();

        if (!$kiosk) {
            abort(403);
        }

        $kiosk->update([
            'last_activity' => now()
        ]);

        return $next($request);
    }
}
