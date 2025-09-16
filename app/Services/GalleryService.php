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
        $query = GalleryImage::with('uploader', 'location')
            ->withCount('likes')
            ->orderByDesc('created_at');

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['location'])) {
            $query->whereHas('location', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['location'] . '%');
            });
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data to match frontend expectations
        $paginated->getCollection()->transform(function ($image) {
            return [
                'id' => $image->id,
                'title' => $image->description ?: 'Untitled Image',
                'location' => $image->location ? $image->location->name : 'Unknown Location',
                'category' => $image->category,
                'image' => $image->file_path,
                'views' => $image->views,
                'likes' => $image->likes->count(),
                'contributor' => $image->uploader ? trim($image->uploader->first_name . ' ' . $image->uploader->last_name) : 'Anonymous',
                'created_at' => $image->created_at,
            ];
        });

        return $paginated;
    }

    public function getImage(int $id): array
    {
        $image = GalleryImage::with('uploader', 'location')
            ->withCount('likes')
            ->findOrFail($id);

        return [
            'id' => $image->id,
            'title' => $image->description ?: 'Untitled Image',
            'location' => $image->location ? $image->location->name : 'Unknown Location',
            'category' => $image->category,
            'image' => $image->file_path,
            'views' => $image->views,
            'likes' => $image->likes->count(),
            'contributor' => $image->uploader ? trim($image->uploader->first_name . ' ' . $image->uploader->last_name) : 'Anonymous',
            'created_at' => $image->created_at,
        ];
    }

    public function uploadImage(UploadedFile $file, array $metadata, User $uploader): GalleryImage
    {
        $filePath = $file->store('gallery', 'public');

        $galleryImage = new GalleryImage();
        $galleryImage->file_path = $filePath;
        $galleryImage->category = $metadata['category'] ?? '';
        $galleryImage->location_id = $metadata['location_id'] ?? null;
        $galleryImage->description = $metadata['description'] ?? null;
        $galleryImage->user_id = $uploader->id;
        $galleryImage->views = 0;
        $galleryImage->save();

        return $galleryImage->load('uploader', 'location');
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

    public function updateImage(int $id, array $data): GalleryImage
    {
        $image = GalleryImage::findOrFail($id);

        $fillableFields = ['category', 'location_id', 'description'];
        $updateData = array_intersect_key($data, array_flip($fillableFields));

        $image->update($updateData);

        return $image->load('uploader', 'location');
    }

    public function deleteImage(int $id): bool
    {
        $image = GalleryImage::findOrFail($id);

        // Delete the physical file from storage
        if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        return $image->delete();
    }

    public function getAdminImages(array $filters): LengthAwarePaginator
    {
        $query = GalleryImage::with('uploader', 'location')
            ->withCount('likes')
            ->orderByDesc('created_at');

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['location'])) {
            $query->whereHas('location', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['location'] . '%');
            });
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('category', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('location', function ($subQ) use ($filters) {
                      $subQ->where('name', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data for admin view
        $paginated->getCollection()->transform(function ($image) {
            return [
                'id' => $image->id,
                'file_path' => $image->file_path,
                'category' => $image->category,
                'location' => $image->location ? $image->location->name : null,
                'description' => $image->description,
                'views' => $image->views,
                'likes_count' => $image->likes_count,
                'uploader' => $image->uploader ? [
                    'id' => $image->uploader->id,
                    'name' => trim($image->uploader->first_name . ' ' . $image->uploader->last_name),
                    'email' => $image->uploader->email,
                ] : null,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,
            ];
        });

        return $paginated;
    }
}
