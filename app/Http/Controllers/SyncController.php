<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SyncController extends Controller
{
    /**
     * Sync user from main application
     * Creates new user or updates existing user based on email or external_id
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function syncUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'external_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country' => 'required|string|size:2',
            'allowed_countries' => 'nullable|array',
            'allowed_countries.*' => 'string|size:2',
            'role' => 'nullable|string',
            'user_type' => 'nullable|in:employee,admin',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Find existing user by external_id or email
        $user = null;
        if (!empty($data['external_id'])) {
            $user = User::where('external_id', $data['external_id'])->first();
        }
        
        if (!$user) {
            $user = User::where('email', $data['email'])->first();
        }

        // Prepare user data
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'country' => strtoupper($data['country']),
            'allowed_countries' => $data['allowed_countries'] ?? [strtoupper($data['country'])],
            'user_type' => $data['user_type'] ?? 'employee',
            'active' => $data['active'] ?? true,
        ];

        if (!empty($data['external_id'])) {
            $userData['external_id'] = $data['external_id'];
        }

        // If user exists, update it
        if ($user) {
            $user->update($userData);
            $message = 'User updated successfully';
        } else {
            // Create new user with auto-generated password
            $userData['password'] = Hash::make(Str::random(32));
            $user = User::create($userData);
            $message = 'User created successfully';
        }

        // Assign role if provided
        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'external_id' => $user->external_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'country' => $user->country,
                    'allowed_countries' => $user->allowed_countries,
                    'user_type' => $user->user_type,
                    'active' => $user->active,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60, // Convert minutes to seconds
            ],
            'message' => $message
        ], $user->wasRecentlyCreated ? 201 : 200);
    }
}

