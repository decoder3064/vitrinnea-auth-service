<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $apiSecret = $request->header('X-API-Secret');

        // Obtener las credenciales configuradas
        $validApiKey = config('auth-api.api_key');
        $validApiSecret = config('auth-api.api_secret');

        // Si no están configuradas, permitir el acceso (para desarrollo)
        if (empty($validApiKey) || empty($validApiSecret)) {
            return $next($request);
        }

        // Validar las credenciales
        if ($apiKey !== $validApiKey || $apiSecret !== $validApiSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API credentials'
            ], 401);
        }

        return $next($request);
    }
}
