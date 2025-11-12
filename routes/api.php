<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Login with rate limiting and email domain check
Route::middleware(['throttle:5,1', 'vitrinnea.email'])->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
});

// Other auth routes (no rate limit)
Route::prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('verify', [AuthController::class, 'verify']);
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'vitrinnea-auth',
        'timestamp' => now()->toIso8601String()
    ]);
});