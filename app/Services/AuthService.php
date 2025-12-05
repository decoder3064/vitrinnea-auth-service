<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

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

    public function forgotPassword(string $email): bool
    {
        // Verificar que el usuario existe
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Por seguridad, no revelar si el email existe o no
            return true;
        }

        // Eliminar tokens previos
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Crear nuevo token
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // TODO: Enviar email con el link
        // Por ahora, solo log del token para testing
        Log::info('Password reset token generated', [
            'email' => $email,
            'token' => $token,
            'reset_url' => config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($email)
        ]);

        // En producción, enviar email:
        // Mail::to($email)->send(new PasswordResetMail($token, $email));

        return true;
    }

    public function resetPassword(string $email, string $token, string $password): bool
    {
        // Buscar el token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return false;
        }

        // Verificar que el token no ha expirado (60 minutos)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        // Verificar el token
        if (!Hash::check($token, $resetRecord->token)) {
            return false;
        }

        // Actualizar la contraseña del usuario
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        $user->password = Hash::make($password);
        $user->save();

        // Eliminar el token usado
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
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
