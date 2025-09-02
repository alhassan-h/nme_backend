<?php

namespace App\Services;

use App\Events\ProductCreated;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getFilteredProducts(array $filters): LengthAwarePaginator
    {
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        $query = Product::with(['seller', 'mineralCategory'])
            ->where('status', Product::STATUS_ACTIVE);

        $query->filter($filters);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with(['seller', 'mineralCategory'])->find($id);
    }

    /**
     * @param array $data
     * @param UploadedFile[]|null $images
     * @return Product
     */
    public function createProduct(array $data, ?array $images): Product
    {
        $user = auth()->user();

        $imagePaths = [];
        if ($images) {
            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }

        $product = new Product();
        $product->title = $data['title'];
        $product->description = $data['description'];
        $product->category = $data['category'];
        $product->price = $data['price'];
        $product->quantity = $data['quantity'];
        $product->unit = $data['unit'];
        $product->location = $data['location'];
        $product->seller_id = $user->id;
        $product->mineral_category_id = $data['mineral_category_id'] ?? null;
        $product->images = $imagePaths;
        $product->status = Product::STATUS_PENDING;
        $product->views = 0;
        $product->min_order = $data['min_order'] ?? null;
        $product->specifications = $data['specifications'] ?? null;
        $product->featured = $data['featured'] ?? false;
        $product->save();

        ProductCreated::dispatch($product);

        return $product->load('seller');
    }

    /**
     * @param Product $product
     * @param array $data
     * @param UploadedFile[]|null $images
     * @return Product
     */
    public function updateProduct(Product $product, array $data, ?array $images): Product
    {
        if (isset($data['title'])) {
            $product->title = $data['title'];
        }
        if (isset($data['description'])) {
            $product->description = $data['description'];
        }
        if (isset($data['category'])) {
            $product->category = $data['category'];
        }
        if (isset($data['mineral_category_id'])) {
            $product->mineral_category_id = $data['mineral_category_id'];
        }
        if (isset($data['price'])) {
            $product->price = $data['price'];
        }
        if (isset($data['quantity'])) {
            $product->quantity = $data['quantity'];
        }
        if (isset($data['unit'])) {
            $product->unit = $data['unit'];
        }
        if (isset($data['location'])) {
            $product->location = $data['location'];
        }
        if (isset($data['min_order'])) {
            $product->min_order = $data['min_order'];
        }
        if (isset($data['specifications'])) {
            $product->specifications = $data['specifications'];
        }
        if (isset($data['featured'])) {
            $product->featured = $data['featured'];
        }
        if ($images) {
            $imagePaths = $product->images ?: [];
            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $product->images = $imagePaths;
        }

        $product->save();

        return $product->load('seller');
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    public function toggleFavorite(Product $product, User $user): void
    {
        $product->toggleFavorite($user);
    }

    public function incrementViews(Product $product): void
    {
        $product->incrementViews();
    }

    public function getUserProducts(int $userId, int $perPage, int $page): LengthAwarePaginator
    {
        return Product::with(['seller', 'mineralCategory'])
            ->where('seller_id', $userId)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUserFavoriteProducts(int $userId, int $perPage, int $page): LengthAwarePaginator
    {
        return Product::with(['seller', 'mineralCategory'])
            ->whereHas('favoritedBy', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function duplicateProduct(Product $product): Product
    {
        $duplicatedProduct = new Product();
        $duplicatedProduct->title = $product->title . ' (Copy)';
        $duplicatedProduct->description = $product->description;
        $duplicatedProduct->category = $product->category;
        $duplicatedProduct->mineral_category_id = $product->mineral_category_id;
        $duplicatedProduct->price = $product->price;
        $duplicatedProduct->quantity = $product->quantity;
        $duplicatedProduct->unit = $product->unit;
        $duplicatedProduct->location = $product->location;
        $duplicatedProduct->seller_id = $product->seller_id;
        $duplicatedProduct->images = $product->images;
        $duplicatedProduct->status = Product::STATUS_PENDING;
        $duplicatedProduct->views = 0;
        $duplicatedProduct->min_order = $product->min_order;
        $duplicatedProduct->specifications = $product->specifications;
        $duplicatedProduct->featured = $product->featured;
        $duplicatedProduct->save();

        return $duplicatedProduct->load('seller');
    }
}
