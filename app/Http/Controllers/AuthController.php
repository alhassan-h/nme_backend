<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        // Return mock data as per context for demo purposes
        return response()->json([
            'message' => 'Login successful (mock)',
            'token' => 'mock-token-string',
            'user' => [
                'id' => 1,
                'name' => 'Mock User',
                'email' => 'mockuser@nme.com',
                'role' => 'user',
                'user_type' => 'buyer',
                'company' => null,
                'phone' => null,
                'location' => null,
                'verified' => true,
                'created_at' => now()->toDateTimeString(),
            ],
        ], Response::HTTP_OK);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request->validated());

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
        ], Response::HTTP_CREATED);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendPasswordResetLink($request->input('email'));

        return response()->json([
            'message' => 'If your email exists in our system, we have sent a password reset link.',
        ]);
    }
}
