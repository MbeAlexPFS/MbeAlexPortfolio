<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return response()->view('errors.403', [], 403);
        }

        if (! $request->user()->isAdmin()) {
            return response()->view('errors.403', [], 403);
        }

        return $next($request);
    }
}
