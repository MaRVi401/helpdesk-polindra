<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key');
        $expectedKey = env('API_KEY_MAHASISWA');

        if (empty($expectedKey) || $apiKey !== $expectedKey) {
            return response()->json([
                'message' => 'Unauthorized: Invalid or missing API Key.'
            ], 401);
        }

        return $next($request);
    }
}