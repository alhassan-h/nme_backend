<?php

namespace App\Services;

use App\Models\MineralCategory;

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
                    'count' => $category->products_count . ' listings',
                ];
            });

        return $categories->toArray();
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