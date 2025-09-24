<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarketInsightRequest;
use App\Http\Requests\Newsletter\CreateNewsletterRequest;
use App\Mail\PasswordResetByAdmin;
use App\Models\User;
use App\Models\UserLoginHistory;
use App\Models\Product;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterRecipient;
use App\Models\MarketInsight;
use App\Models\MarketInsightCategory;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function dashboardStats(): JsonResponse
    {
        try {
            $stats = $this->adminService->getDashboardStats();
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard stats retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function users(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $filters = $request->only(['search', 'status', 'user_type', 'date_from', 'date_to']);
            $users = $this->adminService->paginatedUsers($perPage, $filters);

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showUser(User $user): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $user->load('products', 'forumPosts', 'marketInsights'),
                'message' => 'User retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getUserStats(User $user): JsonResponse
    {
        try {
            $stats = [
                'total_listings' => $user->products()->count(),
                'active_listings' => $user->products()->where('status', 'active')->count(),
                'total_views' => $user->products()->sum('views') ?? 0,
                'total_sales' => $user->products()->where('status', 'sold')->count(),
                'total_forum_posts' => $user->forumPosts()->count(),
                'total_market_insights' => $user->marketInsights()->count(),
                'total_gallery_images' => \DB::table('gallery_images')->where('user_id', $user->id)->count(),
                'last_login' => $user->last_login_at ?? null,
                'account_age_days' => $user->created_at->diffInDays(now()),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'User stats retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserActivity(Request $request, User $user): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $filters = $request->only(['type', 'date_from', 'date_to']);

            // Build activity query from multiple sources
            $activities = collect();

            // Login activities from user_login_history table
            $loginActivities = \DB::table('user_login_history')
                ->where('user_id', $user->id)
                ->where('successful', true)
                ->select([
                    'id',
                    'login_at as timestamp',
                    'ip_address',
                    'device_type',
                    'browser',
                    'operating_system'
                ])
                ->when($filters['date_from'] ?? null, fn($q) => $q->where('login_at', '>=', $filters['date_from']))
                ->when($filters['date_to'] ?? null, fn($q) => $q->where('login_at', '<=', $filters['date_to']))
                ->when($filters['type'] ?? null && $filters['type'] === 'login', fn($q) => $q->whereRaw('1=1')) // Always include for login type
                ->get()
                ->map(function ($item) {
                    $deviceInfo = $this->formatDeviceInfo($item);
                    return [
                        'id' => "login_{$item->id}",
                        'type' => 'login',
                        'description' => 'User logged in',
                        'timestamp' => $item->timestamp,
                        'status' => 'success',
                        'ip_address' => $item->ip_address,
                        'device_info' => $deviceInfo
                    ];
                });

            // Product activities
            $productActivities = $user->products()
                ->selectRaw("'product' as type, title as description, created_at as timestamp, status")
                ->when($filters['date_from'] ?? null, fn($q) => $q->where('created_at', '>=', $filters['date_from']))
                ->when($filters['date_to'] ?? null, fn($q) => $q->where('created_at', '<=', $filters['date_to']))
                ->when($filters['type'] ?? null, fn($q) => $q->where('type', $filters['type']))
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => "product_{$item->id}",
                        'type' => 'product_created',
                        'description' => "Created product: {$item->description}",
                        'timestamp' => $item->timestamp,
                        'status' => $item->status,
                        'ip_address' => null,
                        'device_info' => null
                    ];
                });

            // Forum post activities
            $forumActivities = $user->forumPosts()
                ->selectRaw("'forum' as type, title as description, created_at as timestamp")
                ->when($filters['date_from'] ?? null, fn($q) => $q->where('created_at', '>=', $filters['date_from']))
                ->when($filters['date_to'] ?? null, fn($q) => $q->where('created_at', '<=', $filters['date_to']))
                ->when($filters['type'] ?? null, fn($q) => $q->where('type', $filters['type']))
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => "forum_{$item->id}",
                        'type' => 'forum_post',
                        'description' => "Created forum post: {$item->description}",
                        'timestamp' => $item->timestamp,
                        'status' => 'success',
                        'ip_address' => null,
                        'device_info' => null
                    ];
                });

            // Market insight activities
            $insightActivities = $user->marketInsights()
                ->selectRaw("'insight' as type, title as description, created_at as timestamp, status")
                ->when($filters['date_from'] ?? null, fn($q) => $q->where('created_at', '>=', $filters['date_from']))
                ->when($filters['date_to'] ?? null, fn($q) => $q->where('created_at', '<=', $filters['date_to']))
                ->when($filters['type'] ?? null, fn($q) => $q->where('type', $filters['type']))
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => "insight_{$item->id}",
                        'type' => 'market_insight',
                        'description' => "Created market insight: {$item->description}",
                        'timestamp' => $item->timestamp,
                        'status' => $item->status,
                        'ip_address' => null,
                        'device_info' => null
                    ];
                });

            // Combine and sort activities
            $activities = $loginActivities
                ->concat($productActivities)
                ->concat($forumActivities)
                ->concat($insightActivities)
                ->sortByDesc('timestamp')
                ->values();

            // Paginate manually
            $total = $activities->count();
            $paginatedActivities = $activities->forPage(
                $request->get('page', 1),
                $perPage
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $paginatedActivities,
                    'current_page' => (int) $request->get('page', 1),
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                ],
                'message' => 'User activity retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function parseDeviceInfo(string $name): string
    {
        // Try to extract device info from token name
        // This is a basic implementation - you might want to enhance this
        if (str_contains($name, 'web')) {
            return 'Web Browser';
        } elseif (str_contains($name, 'mobile')) {
            return 'Mobile Device';
        } elseif (str_contains($name, 'api')) {
            return 'API Access';
        }

        return 'Unknown Device';
    }

    private function formatDeviceInfo($item): string
    {
        $deviceInfo = [];

        if ($item->device_type) {
            $deviceInfo[] = ucfirst($item->device_type);
        }

        if ($item->browser) {
            $deviceInfo[] = $item->browser;
        }

        if ($item->operating_system) {
            $deviceInfo[] = $item->operating_system;
        }

        return !empty($deviceInfo) ? implode(' • ', $deviceInfo) : 'Unknown Device';
    }

    public function getUserLoginHistory(Request $request, User $user): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $filters = $request->only(['date_from', 'date_to', 'device_type', 'successful']);

            $query = $user->loginHistory()->with('user');

            // Apply filters
            if (!empty($filters['date_from'])) {
                $query->where('login_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->where('login_at', '<=', $filters['date_to']);
            }

            if (!empty($filters['device_type'])) {
                $query->where('device_type', $filters['device_type']);
            }

            if (isset($filters['successful'])) {
                $query->where('successful', (bool) $filters['successful']);
            }

            // Order by login date descending
            $query->orderBy('login_at', 'desc');

            $loginHistory = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $loginHistory,
                'message' => 'User login history retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user login history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLoginHistory(User $user, UserLoginHistory $loginHistory): JsonResponse
    {
        try {
            // Ensure the login history belongs to the user
            if ($loginHistory->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login history not found for this user'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $loginHistory->load('user'),
                'message' => 'Login history retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve login history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateLoginHistory(Request $request, User $user, UserLoginHistory $loginHistory): JsonResponse
    {
        try {
            // Ensure the login history belongs to the user
            if ($loginHistory->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login history not found for this user'
                ], 404);
            }

            $validated = $request->validate([
                'ip_address' => 'nullable|string|max:45',
                'device_type' => 'nullable|string|in:desktop,mobile,tablet',
                'browser' => 'nullable|string|max:255',
                'operating_system' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'successful' => 'boolean',
                'failure_reason' => 'nullable|string|max:500',
            ]);

            $loginHistory->update($validated);

            return response()->json([
                'success' => true,
                'data' => $loginHistory->load('user'),
                'message' => 'Login history updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update login history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteLoginHistory(User $user, UserLoginHistory $loginHistory): JsonResponse
    {
        try {
            // Ensure the login history belongs to the user
            if ($loginHistory->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login history not found for this user'
                ], 404);
            }

            $loginHistory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Login history deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete login history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserListings(Request $request, User $user): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $filters = $request->only(['status', 'search', 'date_from', 'date_to', 'sort_by', 'sort_order']);

            $query = $user->products()->with('mineralCategory');

            // Apply filters
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            // Apply sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $listings = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $listings,
                'message' => 'User listings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user listings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserGallery(Request $request, User $user): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $filters = $request->only(['type', 'date_from', 'date_to', 'sort_by', 'sort_order']);

            $query = \DB::table('gallery_images')
                ->where('user_id', $user->id)
                ->select('*');

            // Apply filters
            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (!empty($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            // Apply sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $gallery = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $gallery,
                'message' => 'User gallery retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user gallery',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:20',
                'user_type' => 'required|in:buyer,seller,both,admin',
                'company' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUserStatus(Request $request, User $user): JsonResponse
    {
        try {
            $request->validate(['status' => 'required|in:active,inactive,suspended,banned']);

            $updatedUser = $this->adminService->updateUserStatus($user, $request->input('status'));

            return response()->json([
                'success' => true,
                'data' => $updatedUser,
                'message' => 'User status updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetUserPassword(User $user): JsonResponse
    {
        try {
            // Generate a password reset token
            $token = Password::createToken($user);

            // Generate a temporary password and update it
            $tempPassword = \Illuminate\Support\Str::random(12);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($tempPassword)
            ]);

            // Create reset link
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $resetLink = $frontendUrl . '/auth/reset-password?token=' . $token . '&email=' . urlencode($user->email);

            // Send email notification
            try {
                Mail::to($user->email)->send(new PasswordResetByAdmin($user, $resetLink));
            } catch (\Exception $mailException) {
                \Log::error('Failed to send password reset email to user', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $mailException->getMessage()
                ]);

                // Continue with success response but log the email failure
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully, but email notification failed to send.',
                    'data' => [
                        'reset_link' => $resetLink // For testing purposes
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. User has been notified via email.',
                'data' => [
                    'reset_link' => $resetLink // For testing purposes, remove in production
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to reset user password', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadUserAvatar(Request $request, User $user): JsonResponse
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars', $filename, 'public');

                // Delete old avatar if exists
                if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('avatars/' . $user->avatar);
                }

                $user->update(['avatar' => $filename]);

                return response()->json([
                    'success' => true,
                    'data' => $user,
                    'message' => 'Avatar uploaded successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No avatar file provided'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(User $user): JsonResponse
    {
        try {
            // Prevent deletion of admin users
            if ($user->user_type === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete admin users'
                ], 403);
            }

            // Delete user's avatar if exists
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pendingProducts(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $pendingProducts = $this->adminService->getPendingProducts($perPage);

            return response()->json([
                'success' => true,
                'data' => $pendingProducts,
                'message' => 'Pending products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pending products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approveProduct(Product $id): JsonResponse
    {
        try {
            $product = $this->adminService->approveProduct($id);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recentActivity(): JsonResponse
    {
        try {
            $activities = $this->adminService->getRecentActivity();
            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Recent activities retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pendingTasks(): JsonResponse
    {
        try {
            $tasks = $this->adminService->getPendingTasks();
            return response()->json([
                'success' => true,
                'data' => $tasks,
                'message' => 'Pending tasks retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pending tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function insights(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $filters = $request->only(['search', 'status', 'date_from', 'date_to', 'sort_by', 'sort_order']);
            $insights = $this->adminService->paginatedInsights($perPage, $filters);

            return response()->json([
                'success' => true,
                'data' => $insights,
                'message' => 'Insights retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showInsight(MarketInsight $insight): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $insight->load('user', 'category'),
                'message' => 'Insight retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve insight',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createInsight(MarketInsightRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();
            if (isset($validated['status']) && $validated['status'] === 'published') {
                $validated['published_at'] = now();
            }

            $insight = $this->adminService->createInsight($validated);

            return response()->json([
                'success' => true,
                'data' => $insight->load('user', 'category'),
                'message' => 'Insight created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create insight', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create insight',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateInsight(Request $request, MarketInsight $insight): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'nullable|string|max:255',
                'category_name' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer|exists:market_insight_categories,id',
                'featured' => 'boolean',
                'tags' => 'array',
                'price_trend' => 'nullable|string|max:50',
                'market_volume' => 'nullable|string|max:50',
                'investor_confidence' => 'nullable|string|max:50',
                'status' => 'in:draft,published',
            ]);

            // Handle category conversion
            if (isset($validated['category_id'])) {
                $validated['category_id'] = (int) $validated['category_id'];
            } elseif (isset($validated['category_name'])) {
                $category = MarketInsightCategory::where('name', $validated['category_name'])->first();
                if (!$category) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid category',
                        'errors' => ['category' => ['The selected category does not exist.']]
                    ], 422);
                }
                $validated['category_id'] = $category->id;
            } elseif (isset($validated['category'])) {
                $category = MarketInsightCategory::where('name', $validated['category'])->first();
                if (!$category) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid category',
                        'errors' => ['category' => ['The selected category does not exist.']]
                    ], 422);
                }
                $validated['category_id'] = $category->id;
            }

            // Clean up temporary fields
            unset($validated['category'], $validated['category_name']);

            if ($validated['status'] === 'published' && $insight->status !== 'published') {
                $validated['published_at'] = now();
            } elseif ($validated['status'] === 'draft') {
                $validated['published_at'] = null;
            }

            $updatedInsight = $this->adminService->updateInsight($insight, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedInsight->load('user', 'category'),
                'message' => 'Insight updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update insight',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteInsight(MarketInsight $insight): JsonResponse
    {
        try {
            $this->adminService->deleteInsight($insight);

            return response()->json([
                'success' => true,
                'message' => 'Insight deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete insight',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function publishInsight(MarketInsight $insight): JsonResponse
    {
        try {
            $publishedInsight = $this->adminService->publishInsight($insight);

            return response()->json([
                'success' => true,
                'data' => $publishedInsight,
                'message' => 'Insight published successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish insight',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unpublishInsight(MarketInsight $insight): JsonResponse
    {
        try {
            $unpublishedInsight = $this->adminService->unpublishInsight($insight);

            return response()->json([
                'success' => true,
                'data' => $unpublishedInsight,
                'message' => 'Insight unpublished successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpublish insight',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkPublishInsights(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:market_insights,id',
            ]);

            $count = $this->adminService->bulkPublishInsights($validated['ids']);

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
                'message' => "{$count} insights published successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkUnpublishInsights(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:market_insights,id',
            ]);

            $count = $this->adminService->bulkUnpublishInsights($validated['ids']);

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
                'message' => "{$count} insights unpublished successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpublish insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDeleteInsights(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:market_insights,id',
            ]);

            $count = $this->adminService->bulkDeleteInsights($validated['ids']);

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
                'message' => "{$count} insights deleted successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Market Insight Categories CRUD

    public function insightCategories(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $categories = MarketInsightCategory::paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showInsightCategory(MarketInsightCategory $category): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createInsightCategory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:market_insight_categories,name',
                'description' => 'nullable|string',
            ]);

            $category = MarketInsightCategory::create($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateInsightCategory(Request $request, MarketInsightCategory $category): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:market_insight_categories,name,' . $category->id,
                'description' => 'nullable|string',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteInsightCategory(MarketInsightCategory $category): JsonResponse
    {
        try {
            // Check if category has associated insights
            if ($category->marketInsights()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with associated insights'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Newsletter methods

    public function newsletters(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $filters = $request->only(['search', 'status']);
            $newsletters = Newsletter::with('recipients')
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where('subject', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%");
                })
                ->when($filters['status'] ?? null, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $newsletters,
                'message' => 'Newsletters retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve newsletters',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createNewsletter(CreateNewsletterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validatedWithStatus();

            $newsletter = $this->adminService->createNewsletter($validated);

            return response()->json([
                'success' => true,
                'data' => $newsletter,
                'message' => 'Newsletter created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error('Failed to create newsletter', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create newsletter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showNewsletter(Newsletter $newsletter): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $newsletter->load('recipients.subscriber'),
                'message' => 'Newsletter retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve newsletter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateNewsletter(Request $request, Newsletter $newsletter): JsonResponse
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'html_content' => 'nullable|string',
                'scheduled_for' => 'nullable|date|after:now',
            ]);

            $newsletter->update($validated);

            return response()->json([
                'success' => true,
                'data' => $newsletter->load('recipients'),
                'message' => 'Newsletter updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update newsletter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteNewsletter(Newsletter $newsletter): JsonResponse
    {
        try {
            $newsletter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Newsletter deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete newsletter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendNewsletter(Newsletter $newsletter): JsonResponse
    {
        try {
            if ($newsletter->status === 'sent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Newsletter has already been sent'
                ], 400);
            }

            // Use service class to handle sending logic
            $this->adminService->sendNewsletter($newsletter);

            return response()->json([
                'success' => true,
                'data' => $newsletter->load('recipients'),
                'message' => 'Newsletter sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send newsletter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function newsletterSubscribers(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $filters = $request->only(['search', 'status']);
            $subscribers = NewsletterSubscriber::query()
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where('email', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
                })
                ->when($filters['status'] ?? null, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->orderBy('subscribed_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $subscribers,
                'message' => 'Newsletter subscribers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve newsletter subscribers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function newsletterStats(): JsonResponse
    {
        try {
            $totalSubscribers = NewsletterSubscriber::count();
            $activeSubscribers = NewsletterSubscriber::where('status', 'active')->count();
            $unsubscribedSubscribers = NewsletterSubscriber::where('status', 'unsubscribed')->count();
            $totalNewsletters = Newsletter::count();
            $sentNewsletters = Newsletter::where('status', 'sent')->count();

            $avgOpenRate = Newsletter::where('status', 'sent')->get()->avg('open_rate') ?? 0;
            $avgClickRate = Newsletter::where('status', 'sent')->get()->avg('click_rate') ?? 0;

            // Calculate unsubscribe rate
            $unsubscribeRate = $totalSubscribers > 0 ? round(($unsubscribedSubscribers / $totalSubscribers) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_subscribers' => $totalSubscribers,
                    'active_subscribers' => $activeSubscribers,
                    'unsubscribed_subscribers' => $unsubscribedSubscribers,
                    'unsubscribe_rate' => $unsubscribeRate,
                    'total_newsletters' => $totalNewsletters,
                    'sent_newsletters' => $sentNewsletters,
                    'avg_open_rate' => round($avgOpenRate, 2),
                    'avg_click_rate' => round($avgClickRate, 2),
                ],
                'message' => 'Newsletter stats retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve newsletter stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Admin Listings (Products) Management Methods

    public function listings(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $filters = $request->only(['search', 'status', 'category', 'seller_id', 'date_from', 'date_to', 'sort_by', 'sort_order']);

            $query = Product::with(['seller', 'mineralCategory', 'location', 'unit']);

            // Apply filters
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['category'])) {
                $query->where('mineral_category_id', $filters['category']);
            }

            if (!empty($filters['seller_id'])) {
                $query->where('user_id', $filters['seller_id']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('company', 'like', "%{$search}%");
                      });
                });
            }

            if (!empty($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            // Apply sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $listings = $query->paginate($perPage);

            // Transform data for frontend
            $transformedListings = $listings->through(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'category' => $product->mineralCategory?->name ?? 'uncategorized',
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'unit' => $product->unit?->name ?? 'N/A',
                    'location' => $product->location?->name ?? 'N/A',
                    'seller' => $product->seller ? trim($product->seller->first_name . ' ' . $product->seller->last_name) : 'Unknown',
                    'sellerId' => $product->seller_id,
                    'status' => $product->status,
                    'createdAt' => $product->created_at->toISOString(),
                    'updatedAt' => $product->updated_at->toISOString(),
                    'views' => $product->views ?? 0,
                    'inquiries' => 0, // TODO: Add inquiries count if available
                    'isVerified' => $product->seller?->status === 'active' && $product->seller?->email_verified_at !== null,
                ];
            });

            // Calculate stats
            $stats = [
                'total_listings' => Product::count(),
                'active_listings' => Product::where('status', 'active')->count(),
                'pending_listings' => Product::where('status', 'pending')->count(),
                'total_value' => Product::selectRaw('SUM(COALESCE(price, 0) * COALESCE(quantity, 0)) as total_value')->first()->total_value ?? 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $transformedListings,
                'stats' => $stats,
                'message' => 'Listings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve listings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showListing(Product $product): JsonResponse
    {
        try {
            $product->load(['seller', 'mineralCategory', 'location', 'unit']);

            $listing = [
                'id' => $product->id,
                'title' => $product->title,
                'description' => $product->description,
                'category' => $product->mineralCategory?->name ?? 'uncategorized',
                'price' => $product->price,
                'quantity' => $product->quantity,
                'unit' => $product->unit?->name ?? 'N/A',
                'location' => $product->location?->name ?? 'N/A',
                'seller' => $product->seller ? trim($product->seller->first_name . ' ' . $product->seller->last_name) : 'Unknown',
                'sellerId' => $product->seller_id,
                'status' => $product->status,
                'createdAt' => $product->created_at->toISOString(),
                'updatedAt' => $product->updated_at->toISOString(),
                'views' => $product->views ?? 0,
                'inquiries' => 0, // TODO: To add inquiries count when available
                'isVerified' => $product->seller?->status === 'active' && $product->seller?->email_verified_at !== null,
                'mineral_category' => $product->mineralCategory ? ['name' => $product->mineralCategory->name] : null,
                'mineral_category_id' => $product->mineral_category_id,
                'unit_id' => $product->unit_id,
                'location_id' => $product->location_id,
            ];

            return response()->json([
                'success' => true,
                'data' => $listing,
                'message' => 'Listing retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateListing(Request $request, Product $product): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'unit_id' => 'nullable|integer|exists:units,id',
                'location_id' => 'nullable|integer|exists:locations,id',
                'mineral_category_id' => 'nullable|integer|exists:mineral_categories,id',
                'status' => 'required|in:active,pending,suspended,expired,sold',
            ]);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'data' => $product->load(['seller', 'mineralCategory', 'location', 'unit']),
                'message' => 'Listing updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteListing(Product $product): JsonResponse
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Listing deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateListingStatus(Request $request, Product $product): JsonResponse
    {
        try {
            $request->validate(['status' => 'required|in:active,pending,suspended,expired,sold']);

            $product->update(['status' => $request->input('status')]);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Listing status updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update listing status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getListingsValueBreakdown(): JsonResponse
    {
        try {
            \Log::info('Starting listings value breakdown calculation');

            // Get total value by category
            $categoryBreakdown = Product::selectRaw('
                    COALESCE(mineral_categories.name, \'Uncategorized\') as category_name,
                    COUNT(products.id) as listing_count,
                    SUM(COALESCE(products.price, 0) * COALESCE(products.quantity, 0)) as total_value,
                    AVG(products.price) as avg_price,
                    MIN(products.price) as min_price,
                    MAX(products.price) as max_price
                ')
                ->leftJoin('mineral_categories', 'products.mineral_category_id', '=', 'mineral_categories.id')
                ->where('products.status', 'active')
                ->groupBy('mineral_categories.id', 'mineral_categories.name')
                ->orderBy('total_value', 'desc')
                ->get();

            // Get total value by location
            $locationBreakdown = Product::selectRaw('
                    COALESCE(locations.name, \'Unknown Location\') as location_name,
                    COUNT(products.id) as listing_count,
                    SUM(COALESCE(products.price, 0) * COALESCE(products.quantity, 0)) as total_value
                ')
                ->leftJoin('locations', 'products.location_id', '=', 'locations.id')
                ->where('products.status', 'active')
                ->where(function ($query) {
                    $query->whereNull('locations.is_active')
                          ->orWhere('locations.is_active', true);
                })
                ->groupBy('locations.id', 'locations.name')
                ->orderBy('total_value', 'desc')
                ->limit(10)
                ->get();

            // Get top 10 most valuable listings
            $topListings = Product::selectRaw('
                    products.id,
                    products.title,
                    products.price,
                    products.quantity,
                    (COALESCE(products.price, 0) * COALESCE(products.quantity, 0)) as total_value,
                    COALESCE(mineral_categories.name, \'Uncategorized\') as category_name,
                    COALESCE(locations.name, \'Unknown Location\') as location_name,
                    COALESCE(users.first_name, \'\') as first_name,
                    COALESCE(users.last_name, \'\') as last_name
                ')
                ->leftJoin('mineral_categories', 'products.mineral_category_id', '=', 'mineral_categories.id')
                ->leftJoin('locations', 'products.location_id', '=', 'locations.id')
                ->leftJoin('users', 'products.seller_id', '=', 'users.id')
                ->where('products.status', 'active')
                ->orderBy('total_value', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($listing) {
                    return [
                        'id' => $listing->id,
                        'title' => $listing->title,
                        'price' => $listing->price,
                        'quantity' => $listing->quantity,
                        'total_value' => $listing->total_value,
                        'category' => $listing->category_name,
                        'location' => $listing->location_name,
                        'seller' => trim($listing->first_name . ' ' . $listing->last_name) ?: 'Unknown',
                    ];
                });

            // Overall statistics
            $overallStats = [
                'total_value' => Product::where('status', 'active')->selectRaw('SUM(COALESCE(price, 0) * COALESCE(quantity, 0)) as total_value')->first()->total_value ?? 0,
                'total_listings' => Product::where('status', 'active')->count(),
                'avg_listing_value' => Product::where('status', 'active')->selectRaw('AVG(COALESCE(price, 0) * COALESCE(quantity, 0)) as avg_value')->first()->avg_value ?? 0,
                'categories_count' => $categoryBreakdown->count(),
                'locations_count' => $locationBreakdown->count(),
            ];

            // Calculate month-over-month growth (simplified - last 30 days vs previous 30 days)
            $currentMonthValue = Product::where('status', 'active')
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('SUM(COALESCE(price, 0) * COALESCE(quantity, 0)) as total_value')
                ->first()->total_value ?? 0;

            $previousMonthValue = Product::where('status', 'active')
                ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
                ->selectRaw('SUM(COALESCE(price, 0) * COALESCE(quantity, 0)) as total_value')
                ->first()->total_value ?? 0;

            $monthlyGrowth = $previousMonthValue > 0
                ? (($currentMonthValue - $previousMonthValue) / $previousMonthValue) * 100
                : ($currentMonthValue > 0 ? 100 : 0);

            $data = [
                'overall_stats' => $overallStats,
                'category_breakdown' => $categoryBreakdown,
                'location_breakdown' => $locationBreakdown,
                'top_listings' => $topListings,
                'monthly_growth' => round($monthlyGrowth, 2),
                'current_month_value' => $currentMonthValue,
                'previous_month_value' => $previousMonthValue,
            ];

            \Log::info('Listings value breakdown calculation completed', ['data' => $data]);

            return response()->json([
                'success' => true,
                'data' => [
                    'overall_stats' => $overallStats,
                    'category_breakdown' => $categoryBreakdown,
                    'location_breakdown' => $locationBreakdown,
                    'top_listings' => $topListings,
                    'monthly_growth' => round($monthlyGrowth, 2),
                    'current_month_value' => $currentMonthValue,
                    'previous_month_value' => $previousMonthValue,
                ],
                'message' => 'Listings value breakdown retrieved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get listings value breakdown', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve listings value breakdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
