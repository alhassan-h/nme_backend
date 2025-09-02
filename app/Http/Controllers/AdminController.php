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
        $stats = $this->adminService->getDashboardStats();
        return response()->json($stats);
    }

    public function users(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $users = $this->adminService->paginatedUsers($perPage);

        return response()->json($users);
    }

    public function updateUserStatus(Request $request, User $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:active,inactive']);

        $updatedUser = $this->adminService->updateUserStatus($id, $request->input('status'));

        return response()->json($updatedUser);
    }

    public function pendingProducts(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $pendingProducts = $this->adminService->getPendingProducts($perPage);

        return response()->json($pendingProducts);
    }

    public function approveProduct(Product $id): JsonResponse
    {
        $product = $this->adminService->approveProduct($id);

        return response()->json($product);
    }

    public function createNewsletter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'html_content' => 'required|string',
        ]);

        $newsletter = $this->adminService->createNewsletter($validated);

        return response()->json($newsletter, Response::HTTP_CREATED);
    }
}
