<?php

namespace App\Services;

use App\Events\ProductCreated;
use App\Models\Product;
use App\Models\User;
use App\Models\MineralCategory;
use App\Models\Location;
use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $query = Product::with(['seller', 'unit', 'location', 'mineralCategory'])
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
            $cloudinary = app(CloudinaryService::class);
            foreach ($images as $image) {
                try {
                    $result = $cloudinary->upload($image->getRealPath(), ['folder' => 'products']);
                    $imagePaths[] = $result['secure_url'];
                } catch (\Exception $e) {
                    Log::error('Failed to upload product image: ' . $e->getMessage());
                }
            }
        }

        // Find mineral category by name if category is provided
        $mineralCategoryId = null;
        if (isset($data['category'])) {
            $mineralCategory = MineralCategory::where('name', $data['category'])->first();
            $mineralCategoryId = $mineralCategory ? $mineralCategory->id : null;
        } elseif (isset($data['mineral_category_id'])) {
            $mineralCategoryId = $data['mineral_category_id'];
        }

        // Find location by name if location is provided
        if (isset($data['location'])) {
            $data['location'] = trim($data['location']);
            $location = Location::where('name', $data['location'])->first();
            $data['location_id'] = $location ? $location->id : null;
        }else if (isset($data['location_id'])) {
            $data['location_id'] = $data['location_id'];
        }

        // Find unit by name if unit is provided
        if (isset($data['unit'])) {
            $data['unit'] = trim($data['unit']);
            $unit = Unit::where('name', $data['unit'])->first();
            $data['unit_id'] = $unit ? $unit->id : null;
        } else if (isset($data['unit_id'])) {
            $data['unit_id'] = $data['unit_id'];
        }

        $product = new Product();
        $product->title = $data['title'];
        $product->description = $data['description'];
        $product->price = $data['price'];
        $product->quantity = $data['quantity'];
        $product->unit_id = $data['unit_id'];
        $product->location_id = $data['location_id'];
        $product->seller_id = $user->id;
        $product->mineral_category_id = $mineralCategoryId;
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
     * @return boolean
     */
    public function updateProduct(Product $product, array $data, ?array $images): bool
    {
        // Handle image management
        $currentImages = $product->images ?? [];
        $existingImagesToKeep = $data['existing_images'] ?? [];
        $newImages = $images ?? [];

        // Delete removed images from Cloudinary
        foreach ($imagesToDelete as $imageUrl) {
            $this->deleteCloudinaryImage($imageUrl);
        }

        // Upload new images
        $newImagePaths = [];
        if ($newImages) {
            $cloudinary = app(CloudinaryService::class);
            foreach ($newImages as $image) {
                try {
                    $result = $cloudinary->upload($image->getRealPath(), ['folder' => 'products']);
                    $newImagePaths[] = $result['secure_url'];
                } catch (\Exception $e) {
                    Log::error('Failed to upload product image: ' . $e->getMessage());
                }
            }
        }

        // Combine kept existing images with new images
        $finalImages = array_merge($existingImagesToKeep, $newImagePaths);
        $data['images'] = $finalImages;

        // Remove validation fields that aren't part of the model
        unset($data['existing_images']);

        // Update the product directly using fill and save
        $product->fill($data);
        return $product->save();
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

    /**
     * Approve a pending product listing by changing its status to active
     *
     * @param int $productId
     * @return Product
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function approveListing(int $productId): Product
    {
        $product = Product::findOrFail($productId);

        // Only allow approval if the product is currently pending
        if ($product->status !== Product::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending products can be approved');
        }

        $product->status = Product::STATUS_ACTIVE;
        $product->save();

        return $product->load('seller', 'mineralCategory');
    }

    private function deleteCloudinaryImage(string $url): void
    {
        try {
            $path = parse_url($url, PHP_URL_PATH);
            if (!$path) {
                Log::warning('Invalid Cloudinary URL for deletion: ' . $url);
                return;
            }

            $segments = explode('/', ltrim($path, '/'));
            $uploadIndex = array_search('upload', $segments);

            if ($uploadIndex === false) {
                Log::warning('Could not find upload segment in Cloudinary URL: ' . $url);
                return;
            }

            // Get the part after upload/version/
            $publicIdWithExt = implode('/', array_slice($segments, $uploadIndex + 2));
            $publicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);

            $cloudinary = app(CloudinaryService::class);
            $cloudinary->delete($publicId);
        } catch (\Exception $e) {
            Log::error('Failed to delete Cloudinary image: ' . $e->getMessage());
        }
    }
}
