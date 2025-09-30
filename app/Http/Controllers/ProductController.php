<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\OrganizationSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected OrganizationSettingService $settingService;

    public function __construct(ProductService $productService, OrganizationSettingService $settingService)
    {
        $this->productService = $productService;
        $this->settingService = $settingService;
    }

    public function index(Request $request): JsonResponse
    {
        $paginatedProducts = $this->productService->getFilteredProducts($request->all());

        return response()->json($paginatedProducts);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($product);
    }

    public function store(ProductCreateRequest $request): JsonResponse
    {
        // Check if marketplace is enabled
        $this->settingService->checkAccess('marketplace_enabled', 'marketplace');

        $product = $this->productService->createProduct($request->validated(), $request->file('images'));

        // Auto-approve if setting is enabled
        if ($this->settingService->isEnabled('auto_approve_listings')) {
            $product = $this->productService->approveListing($product->id);
        }

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        try {
            $validated = $request->validated();

            $this->productService->updateProduct($product, $validated, $request->file('images'));

            return response()->json([
                'success' => true,
                'data' => $product->load(['seller', 'mineralCategory', 'location', 'unit']),
                'message' => 'Listing updated successfully'
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
                'message' => 'Failed to update listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function toggleFavorite(Product $product): JsonResponse
    {
        $this->productService->toggleFavorite($product, Auth::user());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function incrementView(Product $product): JsonResponse
    {
        $this->productService->incrementViews($product);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function duplicate(Product $product): JsonResponse
    {
        $duplicatedProduct = $this->productService->duplicateProduct($product);

        return response()->json($duplicatedProduct, Response::HTTP_CREATED);
    }
}
