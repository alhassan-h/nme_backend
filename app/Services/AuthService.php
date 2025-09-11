<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Mail\SendPasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Don't reveal if email exists or not for security
            return;
        }

        // Generate a secure token
        $token = Str::random(64);

        // Store the token in the database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send the custom email
        Mail::to($user)->send(new SendPasswordReset($user, $token));
    }

    public function resetPassword(array $credentials): void
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        // Get the token from database
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $credentials['email'])
            ->first();

        if (!$tokenRecord || !Hash::check($credentials['token'], $tokenRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired token.'],
            ]);
        }

        // Check if token is expired (1 hour)
        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            throw ValidationException::withMessages([
                'token' => ['Token has expired. Please request a new password reset.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($credentials['password']),
        ]);

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $credentials['email'])->delete();

        // Log the password reset
        \Log::info('Password reset successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
        ]);
    }
}
