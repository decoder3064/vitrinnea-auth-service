<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function login(string $email, string $password, string $country): ?array
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'active' => true
        ];

        if (!$token = auth()->attempt($credentials)) {
            return null;
        }

        // Verificar que el usuario tenga acceso al país solicitado
        $user = auth()->user();

        if (!$user->hasAccessToCountry($country)) {
            auth()->logout();
            return null;
        }

        // Establecer el país seleccionado y regenerar token con el claim correcto
        auth()->logout();
        $user->selectedCountry = $country;
        $token = auth()->login($user);

        return $this->respondWithToken($token, $country);
    }

    public function register(array $userData): ?array
    {
        $defaultCountry = $userData['country'] ?? 'SV';

        // Crear usuario
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'user_type' => $userData['user_type'] ?? 'employee',
            'country' => $defaultCountry,
            'allowed_countries' => $userData['allowed_countries'] ?? [$defaultCountry],
            'active' => true,
            'email_verified_at' => now(),
        ]);

        // Asignar rol por defecto
        $defaultRole = $userData['role'] ?? 'User';
        $user->assignRole($defaultRole);

        // Establecer país por defecto para el token
        $user->selectedCountry = $defaultCountry;

        // Generar token JWT
        $token = auth()->login($user);

        return $this->respondWithToken($token, $defaultCountry);
    }

    public function logout(): void
    {
        auth()->logout();
    }

    public function refresh(): array
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function me(): User
    {
        return auth()->user()->load('roles.permissions', 'groups');
    }

    public function verify(string $token): ?array
    {
        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            
            $user = User::find($payload->get('sub'));
            
            if (!$user || !$user->active) {
                return null;
            }

            $user->load('roles.permissions', 'groups');

            return [
                'valid' => true,
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'user_type' => $user->user_type,
                'country' => $user->country,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'groups' => $user->groups->pluck('name'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function respondWithToken(string $token, ?string $selectedCountry = null): array
    {
        $user = auth()->user()->load('roles.permissions', 'groups');

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'country' => $selectedCountry ?? $user->country,
                'allowed_countries' => $user->allowed_countries ?? [$user->country],
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'groups' => $user->groups->pluck('name'),
            ],
        ];
    }
}