<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Models\UserLoginHistory;
use App\Services\AuthService;
use App\Services\OrganizationSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected OrganizationSettingService $settingService;

    public function __construct(AuthService $authService, OrganizationSettingService $settingService)
    {
        $this->authService = $authService;
        $this->settingService = $settingService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->loginUser($request->only(['email', 'password']));

        if (!$user) {
            // Log failed login attempt
            $email = $request->input('email');
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                UserLoginHistory::logLogin(
                    $existingUser,
                    $request->ip(),
                    $request->userAgent(),
                    false,
                    'Invalid credentials'
                );
            }

            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Log successful login
        UserLoginHistory::logLogin(
            $user,
            $request->ip(),
            $request->userAgent(),
            true
        );

        // Update user's last login timestamp
        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Check if registration is enabled
            $this->settingService->checkAccess('registration_enabled', 'user registration');

            $user = $this->authService->registerUser($request->validated());

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'token' => $token,
                'user' => $user,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendPasswordResetLink($request->input('email'));

        return response()->json([
            'message' => 'If your email exists in our system, we have sent a password reset link.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword($request->only(['email', 'token', 'password', 'password_confirmation']));

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
