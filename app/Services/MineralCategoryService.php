<?php

namespace App\Services;

use App\Models\MineralCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MineralCategoryService
{
    /**
     * Get all active mineral categories with dynamic product counts
     */
    public function getCategoriesWithCounts(): array
    {
        $categories = MineralCategory::active()
            ->ordered()
            ->withCount('products')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'count' => $category->products . ' listings',
                ];
            });

        return $categories->toArray();
    }

    /**
     * Get all mineral categories with pagination for admin
     */
    public function getCategories(array $filters = []): LengthAwarePaginator
    {
        $query = MineralCategory::query();
        $query->withCount('products');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('icon', 'like', '%' . $filters['search'] . '%');
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
     * Get all active categories for dropdowns
     */
    public function getActiveCategories(): array
    {
        return MineralCategory::active()->ordered()->get()->toArray();
    }

    /**
     * Get a specific category by ID
     */
    public function getCategory(int $id): MineralCategory
    {
        return MineralCategory::findOrFail($id);
    }

    /**
     * Get a specific category with its products
     */
    public function getCategoryWithProducts(int $categoryId): ?MineralCategory
    {
        return MineralCategory::with(['products' => function ($query) {
            $query->where('status', 'active')
                  ->orderBy('created_at', 'desc');
        }])->find($categoryId);
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data): MineralCategory
    {
        return MineralCategory::create($data);
    }

    /**
     * Update an existing category
     */
    public function updateCategory(int $id, array $data): MineralCategory
    {
        $category = MineralCategory::findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete a category
     */
    public function deleteCategory(int $id): bool
    {
        $category = MineralCategory::findOrFail($id);
        return $category->delete();
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(int $id): MineralCategory
    {
        $category = MineralCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats(): array
    {
        $totalCategories = MineralCategory::active()->count();
        $totalProducts = MineralCategory::withCount('products')
            ->get()
            ->sum('products_count');

        return [
            'total_categories' => $totalCategories,
            'total_products' => $totalProducts,
        ];
    }
}