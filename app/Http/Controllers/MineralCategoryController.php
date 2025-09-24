<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMineralCategoryRequest;
use App\Http\Requests\UpdateMineralCategoryRequest;
use App\Services\MineralCategoryService;
use Illuminate\Http\JsonResponse;

class MineralCategoryController extends Controller
{
    protected $mineralCategoryService;

    public function __construct(MineralCategoryService $mineralCategoryService)
    {
        $this->mineralCategoryService = $mineralCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = $this->mineralCategoryService->getActiveCategories();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMineralCategoryRequest $request): JsonResponse
    {
        $category = $this->mineralCategoryService->createCategory($request->validated());
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->mineralCategoryService->getCategory($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMineralCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->mineralCategoryService->updateCategory($id, $request->validated());
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->mineralCategoryService->deleteCategory($id);
        return response()->json(['message' => 'Mineral Category deleted successfully']);
    }

    /**
     * Get categories for admin with pagination
     */
    public function adminIndex(): JsonResponse
    {
        $filters = request()->only(['search', 'per_page', 'page']);
        $categories = $this->mineralCategoryService->getCategories($filters);
        return response()->json($categories);
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(int $id): JsonResponse
    {
        $category = $this->mineralCategoryService->toggleActive($id);
        return response()->json($category);
    }

    /**
     * Get categories with counts (for frontend)
     */
    public function getWithCounts(): JsonResponse
    {
        $categories = $this->mineralCategoryService->getCategoriesWithCounts();
        return response()->json($categories);
    }
}
