<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Attempt to log in a user
     */
    public function login(string $email, string $password): ?array
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'active' => true
        ];

        if (!$token = auth()->attempt($credentials)) {
            return null;
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log out the authenticated user
     */
    public function logout(): void
    {
        auth()->logout();
    }

    /**
     * Refresh the JWT token
     */
    public function refresh(): array
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the authenticated user with roles and permissions
     */
    public function me(): User
    {
        return auth()->user()->load('roles.permissions');
    }

    /**
     * Verify a JWT token and return user data
     */
    public function verify(string $token): ?array
    {
        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            
            $user = User::find($payload->get('sub'));
            
            if (!$user || !$user->active) {
                return null;
            }

            return [
                'valid' => true,
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'user_type' => $user->user_type,
                'country' => $user->country,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format the token response
     */
    protected function respondWithToken(string $token): array
    {
        $user = auth()->user()->load('roles.permissions');

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'country' => $user->country,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ];
    }
}