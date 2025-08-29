<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function registerUser(array $attributes): User
    {
        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'user_type' => $attributes['user_type'],
            'company' => $attributes['company'] ?? null,
            'phone' => $attributes['phone'] ?? null,
            'location' => $attributes['location'] ?? null,
            'verified' => false,
            'role' => 'user',
        ]);
        UserRegistered::dispatch($user);

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
