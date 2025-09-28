<?php

namespace App\Http\Controllers;

use App\Mail\SendEmailVerification;
use App\Models\User;
use App\Services\ProductService;
use App\Services\GalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected ProductService $productService;
    protected GalleryService $galleryService;

    public function __construct(ProductService $productService, GalleryService $galleryService)
    {
        $this->middleware('auth:sanctum');
        $this->productService = $productService;
        $this->galleryService = $galleryService;
    }

    public function products(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $products = $this->productService->getUserProducts($user->id, $perPage, $page);

        return response()->json($products);
    }

    public function favorites(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $favorites = $this->productService->getUserFavoriteProducts($user->id, $perPage, $page);

        return response()->json($favorites);
    }

    public function gallery(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $gallery = $this->galleryService->getUserGallery($user->id, $perPage, $page);

        return response()->json($gallery);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'company', 'bio', 'website'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:1',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            \Log::warning('Password change validation failed', [
                'user_id' => $user->id,
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {

            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // TODO: Send confirmation email
            

            return response()->json([
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to update password. Please try again.'
            ], 500);
        }
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => 'Avatar uploaded successfully',
                'avatar_url' => Storage::url($path),
                'user' => $user->fresh()
            ]);
        }

        return response()->json([
            'message' => 'No file uploaded'
        ], 400);
    }

    public function sendEmailVerification(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->verified) {
            return response()->json([
                'message' => 'Email is already verified'
            ], 400);
        }

        try {
            Mail::to($user)->send(new SendEmailVerification($user));

            return response()->json([
                'message' => 'Verification email sent successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to send verification email. Please try again.'
            ], 500);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Simple token verification (in production, use more secure method)
        $expectedToken = sha1($user->email . $user->id . config('app.key'));

        if ($request->token !== $expectedToken) {
            return response()->json([
                'message' => 'Invalid verification token'
            ], 400);
        }

        if ($user->verified) {
            return response()->json([
                'message' => 'Email is already verified'
            ], 400);
        }

        $user->update([
            'verified' => true,
            'email_verified_at' => now(),
        ]);

        \Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'message' => 'Email verified successfully',
            'user' => $user->fresh()
        ]);
    }
}
