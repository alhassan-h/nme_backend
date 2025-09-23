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

    public function adminShow(int $id): JsonResponse
    {
        $image = $this->galleryService->getAdminImage($id);

        return response()->json($image);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'category' => 'required|string|max:255',
            'location_id' => 'required|numeric|max:255',
            'description' => 'nullable|string',
        ]);

        $image = $request->file('image');
        $metadata = $request->only(['category', 'location_id', 'description']);
        $uploader = Auth::user();

        $galleryImage = $this->galleryService->uploadImage($image, $metadata, $uploader);

        return response()->json($galleryImage, Response::HTTP_CREATED);
    }

    public function toggleLike(int $id): JsonResponse
    {
        $user = Auth::user();
        $liked = $this->galleryService->toggleLike($id, $user->id);

        // Get updated like count
        $image = $this->galleryService->getImage($id);

        return response()->json([
            'liked' => $liked,
            'likes_count' => $image['likes_count']
        ]);
    }

    public function incrementView(int $id): JsonResponse
    {
        $this->galleryService->incrementView($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['category', 'location', 'description']);
        $image = $this->galleryService->updateImage($id, $data);

        return response()->json($image);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->galleryService->deleteImage($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->get('category'),
            'location' => $request->get('location'),
            'search' => $request->get('search'),
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 15),
        ];

        $paginated = $this->galleryService->getAdminImages($filters);

        return response()->json($paginated);
    }

    public function approve(int $id): JsonResponse
    {
        try {
            $image = $this->galleryService->approveImage($id);
            return response()->json($image);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery image not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to approve image'], 500);
        }
    }

    public function publish(int $id): JsonResponse
    {
        try {
            $image = $this->galleryService->publishImage($id);
            return response()->json($image);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery image not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to publish image'], 500);
        }
    }

    public function unpublish(int $id): JsonResponse
    {
        try {
            $image = $this->galleryService->unpublishImage($id);
            return response()->json($image);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery image not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to unpublish image'], 500);
        }
    }

    public function hide(int $id): JsonResponse
    {
        try {
            $image = $this->galleryService->hideImage($id);
            return response()->json($image);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery image not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to hide image'], 500);
        }
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:published,pending,unpublished,hidden',
        ]);

        $image = $this->galleryService->updateImageStatus($id, $request->status);
        return response()->json($image);
    }

    public function serveImage(string $filename)
    {
        $path = storage_path('app/public/gallery/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    public function checkLikeStatus(int $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['liked' => false]);
        }

        $liked = $this->galleryService->checkUserLikeStatus($id, $user->id);
        return response()->json(['liked' => $liked]);
    }
}
