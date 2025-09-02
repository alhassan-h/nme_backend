<?php

namespace App\Services;

use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    public function getImages(array $filters): LengthAwarePaginator
    {
        $query = GalleryImage::with('uploader')
            ->withCount('likes')
            ->orderByDesc('created_at');

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data to match frontend expectations
        $paginated->getCollection()->transform(function ($image) {
            return [
                'id' => $image->id,
                'title' => $image->description ?: 'Untitled Image',
                'location' => $image->location ?: 'Unknown Location',
                'category' => $image->category,
                'image' => $image->file_path,
                'views' => $image->views,
                'likes' => $image->likes->count(),
                'contributor' => $image->uploader->name ?? 'Anonymous',
                'created_at' => $image->created_at,
            ];
        });

        return $paginated;
    }

    public function uploadImage(UploadedFile $file, array $metadata, User $uploader): GalleryImage
    {
        $filePath = $file->store('gallery', 'public');

        $galleryImage = new GalleryImage();
        $galleryImage->file_path = $filePath;
        $galleryImage->category = $metadata['category'] ?? '';
        $galleryImage->location = $metadata['location'] ?? null;
        $galleryImage->description = $metadata['description'] ?? null;
        $galleryImage->user_id = $uploader->id;
        $galleryImage->views = 0;
        $galleryImage->likes_count = 0;
        $galleryImage->save();

        return $galleryImage->load('uploader');
    }

    public function toggleLike(int $galleryImageId, int $userId): void
    {
        $image = GalleryImage::findOrFail($galleryImageId);
        $image->toggleLike($userId);
    }

    public function incrementView(int $galleryImageId): void
    {
        $image = GalleryImage::findOrFail($galleryImageId);
        $image->incrementView();
    }
}
