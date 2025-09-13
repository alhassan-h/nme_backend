<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarketInsightRequest;
use App\Models\User;
use App\Models\Product;
use App\Models\Newsletter;
use App\Models\MarketInsight;
use App\Models\MarketInsightCategory;
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
}
