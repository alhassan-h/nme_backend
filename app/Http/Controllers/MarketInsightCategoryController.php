<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarketInsightCategoryRequest;
use App\Http\Requests\UpdateMarketInsightCategoryRequest;
use App\Services\MarketInsightCategoryService;
use Illuminate\Http\JsonResponse;

class MarketInsightCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(MarketInsightCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketInsightCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategory($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketInsightCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return response()->json(['message' => 'Market Insight Category deleted successfully']);
    }

    /**
     * Get categories for admin with pagination
     */
    public function adminIndex(): JsonResponse
    {
        $filters = request()->only(['search', 'per_page', 'page']);
        $categories = $this->categoryService->getCategories($filters);
        return response()->json($categories);
    }

    /**
     * Get category with insights count
     */
    public function getWithInsightsCount(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryWithInsightsCount($id);
        return response()->json($category);
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(int $id): JsonResponse
    {
        $category = $this->categoryService->toggleActive($id);
        return response()->json($category);
    }
}