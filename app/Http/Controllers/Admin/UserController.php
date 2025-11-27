<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordResetEmail;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roles', 'groups']);

        if ($request->has('country')) {
            $countryCode = strtoupper($request->country);
            // Buscar usuarios que tengan acceso al país especificado
            $query->where(function($q) use ($countryCode) {
                $q->where('country', $countryCode)
                  ->orWhereJsonContains('allowed_countries', $countryCode);
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with(['roles', 'groups'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|ends_with:@vitrinnea.com',
            'country' => 'required|string|size:2',
            'allowed_countries' => 'nullable|array|min:1',
            'allowed_countries.*' => 'string|size:2',
            'user_type' => 'required|in:employee,customer,api_client',
            'role' => 'nullable|string|exists:roles,name',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
            'send_welcome_email' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $temporaryPassword = Str::random(12);

        // Si no se envían allowed_countries, usar el country como único país permitido
        $allowedCountries = $request->has('allowed_countries')
            ? array_map('strtoupper', $request->allowed_countries)
            : [strtoupper($request->country)];

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'country' => strtoupper($request->country),
            'allowed_countries' => $allowedCountries,
            'user_type' => $request->user_type,
            'active' => true,
        ]);

        if ($request->has('role')) {
            $user->assignRole($request->role);
        }

        if ($request->has('groups')) {
            $user->groups()->attach($request->groups);
        }

        if ($request->boolean('send_welcome_email', true)) {
            try {
                Mail::to($user->email)->send(new WelcomeEmail($user, $temporaryPassword));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data' => $user->load(['roles', 'groups']),
            'message' => 'User created successfully. Temporary password has been sent via email.',
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id . '|ends_with:@vitrinnea.com',
            'country' => 'sometimes|string|size:2',
            'allowed_countries' => 'sometimes|array|min:1',
            'allowed_countries.*' => 'string|size:2',
            'user_type' => 'sometimes|in:employee,customer,api_client',
            'active' => 'sometimes|boolean',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Convertir allowed_countries a uppercase para consistencia
        $updateData = $request->only(['name', 'email', 'country', 'user_type', 'active']);

        if ($request->has('allowed_countries')) {
            $updateData['allowed_countries'] = array_map('strtoupper', $request->allowed_countries);
        }

        $user->update($updateData);

        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'success' => true,
            'data' => $user->load(['roles', 'groups']),
            'message' => 'User updated successfully'
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update(['active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'User deactivated successfully',
            'data' => $user->load(['roles', 'groups'])
        ]);
    }

    public function activate(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update(['active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'User activated successfully',
            'data' => $user->load(['roles', 'groups'])
        ]);
    }

    public function assignGroups(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->groups()->sync($request->groups);

        return response()->json([
            'success' => true,
            'data' => $user->load('groups'),
            'message' => 'Groups assigned successfully'
        ]);
    }

    public function resetPassword(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $newPassword = Str::random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        try {
            Mail::to($user->email)->send(new PasswordResetEmail($user, $newPassword));
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed. Could not send email.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. New temporary password has been sent via email.',
        ]);
    }

    public function getAvailableCountries(): JsonResponse
    {
        // Lista de países disponibles en Vitrinnea
        $countries = [
            ['code' => 'SV', 'name' => 'El Salvador'],
            ['code' => 'GT', 'name' => 'Guatemala'],
            ['code' => 'CR', 'name' => 'Costa Rica'],
            ['code' => 'HN', 'name' => 'Honduras'],
            ['code' => 'NI', 'name' => 'Nicaragua'],
            ['code' => 'PA', 'name' => 'Panamá'],
        ];

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }
}
