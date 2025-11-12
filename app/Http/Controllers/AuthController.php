<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller implements HasMiddleware
{
    public function __construct(private AuthService $authService)
    {
        //
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login', 'verify']),
        ];
    }

    /**
     * User login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',  
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or inactive account'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Login successful'
        ]);
    }

    /**
     * Get authenticated user info
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->authService->me()
        ]);
    }

    /**
     * Log out user
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh JWT token
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->authService->refresh()
        ]);
    }

    /**
     * Verify JWT token (for other services)
     */
    public function verify(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided'
            ], 401);
        }

        $result = $this->authService->verify($token);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}