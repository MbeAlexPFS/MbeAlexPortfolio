<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Setting::isMaintenance()) {
            return $next($request);
        }

        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        if ($request->is('maintenance/login')) {
            return $next($request);
        }

        return response()->view('maintenance');
    }
}
