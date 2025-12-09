<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\SyncController;

Route::middleware(['throttle:5,1', 'vitrinnea.email', 'api.key'])->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
});

// Service-to-service sync endpoint
Route::middleware(['api.key'])->group(function () {
    Route::post('sync/user', [SyncController::class, 'syncUser']);
});

Route::prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::get('health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'vitrinnea-auth',
        'timestamp' => now()->toIso8601String()
    ]);
});

Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/countries', [UserController::class, 'getAvailableCountries']);
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/activate', [UserController::class, 'activate']);
        Route::post('/{id}/groups', [UserController::class, 'assignGroups']);
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword']);
    });

    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::post('/', [GroupController::class, 'store']);
        Route::get('/{id}', [GroupController::class, 'show']);
        Route::delete('/{id}', [GroupController::class, 'destroy']);
    });
});