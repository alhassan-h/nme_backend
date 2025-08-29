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

        $query = Product::with('seller')
            ->where('status', Product::STATUS_ACTIVE);

        $query->filter($filters);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('seller')->find($id);
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
        $product->images = $imagePaths;
        $product->status = Product::STATUS_PENDING;
        $product->views = 0;
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
        return Product::with('seller')
            ->where('seller_id', $userId)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUserFavoriteProducts(int $userId, int $perPage, int $page): LengthAwarePaginator
    {
        return Product::with('seller')
            ->whereHas('favoritedBy', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
