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
            new Middleware('auth:api', except: ['login', 'register', 'verify', 'forgotPassword', 'resetPassword']),
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

        // Validar que el header x-country esté presente
        $country = $request->header('x-country');

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Header x-country is required'
            ], 400);
        }

        // Validar formato del código de país (2 letras)
        if (!preg_match('/^[A-Z]{2}$/i', $country)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid country code format. Expected 2-letter ISO code (e.g., SV, GT, CR)'
            ], 400);
        }

        $result = $this->authService->login(
            $request->email,
            $request->password,
            strtoupper($country)
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials, inactive account, or no access to the specified country'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Login successful'
        ]);
    }

    /**
     * User registration
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'nullable|in:employee,admin',
            'country' => 'nullable|string|size:2',
            'allowed_countries' => 'nullable|array',
            'allowed_countries.*' => 'string|size:2',
            'role' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->authService->register($request->all());

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Registration successful'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Request password reset
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->forgotPassword($request->email);

        return response()->json([
            'success' => true,
            'message' => 'If your email exists in our system, you will receive a password reset link'
        ]);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully'
        ]);
    }
}