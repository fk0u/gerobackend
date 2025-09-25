<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:mitra') or multiple roles role:mitra,end_user
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $allowed = array_map('trim', explode(',', $roles));
        $user = $request->user();
        if (!$user || !in_array($user->role, $allowed, true)) {
            return response()->json(['error' => [
                'code' => 'forbidden',
                'message' => 'You do not have permission for this resource.'
            ]], 403);
        }
        return $next($request);
    }
}