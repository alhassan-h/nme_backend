<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->middleware('auth:sanctum');
        $this->productService = $productService;
    }

    public function products(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $products = $this->productService->getUserProducts($user->id, $perPage, $page);

        return response()->json($products);
    }

    public function favorites(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $favorites = $this->productService->getUserFavoriteProducts($user->id, $perPage, $page);

        return response()->json($favorites);
    }
}
