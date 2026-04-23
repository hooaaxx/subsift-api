<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Cache::get('maintenance_mode', false) && ! $request->user()?->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Under maintenance.',
            ], 503);
        }

        return $next($request);
    }
}
