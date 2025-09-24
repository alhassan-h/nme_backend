<?php

namespace App\Services;

use App\Models\MarketInsightCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarketInsightCategoryService
{
    /**
     * Get all market insight categories with pagination for admin
     */
    public function getCategories(array $filters = []): LengthAwarePaginator
    {
        $query = MarketInsightCategory::query();
        $query->withCount('marketInsights');
        
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->orderBy('name')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get all categories for dropdowns
     */
    public function getAllCategories(): array
    {
        return MarketInsightCategory::orderBy('name')->get()->toArray();
    }

    /**
     * Get a specific category by ID
     */
    public function getCategory(int $id): MarketInsightCategory
    {
        return MarketInsightCategory::findOrFail($id);
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data): MarketInsightCategory
    {
        return MarketInsightCategory::create($data);
    }

    /**
     * Update an existing category
     */
    public function updateCategory(int $id, array $data): MarketInsightCategory
    {
        $category = MarketInsightCategory::findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete a category
     */
    public function deleteCategory(int $id): bool
    {
        $category = MarketInsightCategory::findOrFail($id);
        return $category->delete();
    }

    /**
     * Get category with insights count
     */
    public function getCategoryWithInsightsCount(int $id): array
    {
        $category = MarketInsightCategory::withCount('marketInsights')->findOrFail($id);
        return $category->toArray();
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(int $id): MarketInsightCategory
    {
        $category = MarketInsightCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }
}