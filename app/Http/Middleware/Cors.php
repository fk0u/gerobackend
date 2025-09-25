<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $origin = $request->headers->get('Origin', '*');

        $headers = [
            'Access-Control-Allow-Origin' => $origin ?: '*',
            'Vary' => 'Origin',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Max-Age' => '86400',
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Handle preflight
        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
        }

        return $response;
    }
}
