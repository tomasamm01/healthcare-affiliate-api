<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $userRole = $request->user()->role ?? 'viewer';

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'You do not have permission to access this resource',
            ], 403);
        }

        return $next($request);
    }
}
