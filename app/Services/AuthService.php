<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function registerUser(array $attributes): User
    {
        $company = $attributes['company'] ?? $attributes['company_name'] ?? null;

        $user = User::create([
            'first_name' => $attributes['first_name'],
            'last_name' => $attributes['last_name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'user_type' => $attributes['user_type'],
            'company' => $company,
            'phone' => $attributes['phone'] ?? null,
            'location' => $attributes['location'] ?? null,
            'verified' => false,
        ]);
        UserRegistered::dispatch($user);

        return $user;
    }

    public function loginUser(array $credentials): ?User
    {
        // For API authentication, manually verify credentials
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    public function sendPasswordResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }
    }
}
