<?php

namespace App\Http\Controllers;

use App\Services\GalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class GalleryController extends Controller
{
    protected GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->get('category'),
            'location' => $request->get('location'),
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 15),
        ];

        $paginated = $this->galleryService->getImages($filters);

        return response()->json($paginated);
    }

    public function show(int $id): JsonResponse
    {
        $image = $this->galleryService->getImage($id);

        return response()->json($image);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'category' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $image = $request->file('image');
        $metadata = $request->only(['category', 'location', 'description']);
        $uploader = Auth::user();

        $galleryImage = $this->galleryService->uploadImage($image, $metadata, $uploader);

        return response()->json($galleryImage, Response::HTTP_CREATED);
    }

    public function toggleLike(int $id): JsonResponse
    {
        $user = Auth::user();
        $this->galleryService->toggleLike($id, $user->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function incrementView(int $id): JsonResponse
    {
        $this->galleryService->incrementView($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
