<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Newsletter;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $users = $this->adminService->paginatedUsers($perPage);

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

    public function updateUserStatus(Request $request, User $id): JsonResponse
    {
        try {
            $request->validate(['status' => 'required|in:active,inactive']);

            $updatedUser = $this->adminService->updateUserStatus($id, $request->input('status'));

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

    public function createNewsletter(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'html_content' => 'required|string',
            ]);

            $newsletter = $this->adminService->createNewsletter($validated);

            return response()->json([
                'success' => true,
                'data' => $newsletter,
                'message' => 'Newsletter created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create newsletter',
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
}
