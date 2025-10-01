<?php

namespace App\Services;

use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GalleryService
{
    public function getImages(array $filters): LengthAwarePaginator
    {
        $query = GalleryImage::with('uploader', 'location')
            ->withCount('likes')
            ->where('status', 'published')
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
                'title' => ucfirst($image->category) . ' Image',
                'description' => $image->description,
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
            ->where('status', 'published')
            ->findOrFail($id);

        return [
            'id' => $image->id,
            'title' => ucfirst($image->category) . ' Image',
            'description' => $image->description,
            'location' => $image->location ? $image->location->name : null,
            'location_id' => $image->location_id,
            'category' => $image->category,
            'file_path' => $image->file_path,
            'views' => $image->views,
            'likes_count' => $image->likes->count(),
            'contributor' => $image->uploader ? trim($image->uploader->first_name . ' ' . $image->uploader->last_name) : 'Anonymous',
            'created_at' => $image->created_at,
        ];
    }

    public function getAdminImage(int $id): array
    {
        $image = GalleryImage::with('uploader', 'location')
            ->withCount('likes')
            ->findOrFail($id);

        return [
            'id' => $image->id,
            'title' => ucfirst($image->category) . ' Image',
            'description' => $image->description,
            'location' => $image->location ? $image->location->name : null,
            'location_id' => $image->location_id,
            'category' => $image->category,
            'file_path' => $image->file_path,
            'views' => $image->views,
            'likes_count' => $image->likes->count(),
            'status' => $image->status,
            'contributor' => $image->uploader ? trim($image->uploader->first_name . ' ' . $image->uploader->last_name) : 'Anonymous',
            'created_at' => $image->created_at,
            'updated_at' => $image->updated_at,
        ];
    }

    public function uploadImage(UploadedFile $file, array $metadata, User $uploader): GalleryImage
    {
        $cloudinary = app(CloudinaryService::class);

        try {
            $result = $cloudinary->upload($file->getRealPath(), ['folder' => 'gallery']);
            $filePath = $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Failed to upload gallery image: ' . $e->getMessage());
            throw new \Exception('Failed to upload image');
        }

        $galleryImage = new GalleryImage();
        $galleryImage->file_path = $filePath;
        $galleryImage->category = $metadata['category'] ?? '';
        $galleryImage->location_id = $metadata['location_id'] ?? null;
        $galleryImage->description = $metadata['description'] ?? null;
        $galleryImage->user_id = $uploader->id;
        $galleryImage->views = 0;
        $galleryImage->status = 'pending';
        $galleryImage->save();

        return $galleryImage->load('uploader', 'location');
    }

    public function toggleLike(int $galleryImageId, int $userId): bool
    {
        $image = GalleryImage::findOrFail($galleryImageId);
        return $image->toggleLike($userId);
    }

    public function incrementView(int $galleryImageId): void
    {
        $image = GalleryImage::findOrFail($galleryImageId);
        $image->incrementView();
    }

    public function updateImage(int $id, array $data, ?UploadedFile $newImage = null, ?User $uploader = null): GalleryImage
    {
        $image = GalleryImage::findOrFail($id);

        $fillableFields = ['category', 'location_id', 'description'];
        $updateData = array_intersect_key($data, array_flip($fillableFields));

        // Handle image replacement
        if ($newImage) {
            $cloudinary = app(CloudinaryService::class);

            try {
                // Delete old image
                if ($image->file_path) {
                    $this->deleteCloudinaryImage($image->file_path);
                }

                $result = $cloudinary->upload($newImage->getRealPath(), ['folder' => 'gallery']);
                $filePath = $result['secure_url'];
                $updateData['file_path'] = $filePath;

                // Update uploader if provided
                if ($uploader) {
                    $updateData['user_id'] = $uploader->id;
                }
            } catch (\Exception $e) {
                Log::error('Failed to upload updated gallery image: ' . $e->getMessage());
                throw new \Exception('Failed to upload image');
            }
        }

        $image->update($updateData);

        return $image->load('uploader', 'location');
    }

    public function deleteImage(int $id): bool
    {
        $image = GalleryImage::findOrFail($id);

        // Note: Cloudinary images are not deleted from storage here
        // They can be deleted via Cloudinary API if needed

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
                'status' => $image->status,
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

    public function updateImageStatus(int $id, string $status): GalleryImage
    {
        $image = GalleryImage::findOrFail($id);

        $validStatuses = ['published', 'pending', 'unpublished', 'hidden'];
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Invalid status provided');
        }

        $image->update(['status' => $status]);

        return $image->load('uploader', 'location');
    }

    public function approveImage(int $id): GalleryImage
    {
        return $this->updateImageStatus($id, 'published');
    }

    public function publishImage(int $id): GalleryImage
    {
        return $this->updateImageStatus($id, 'published');
    }

    public function unpublishImage(int $id): GalleryImage
    {
        return $this->updateImageStatus($id, 'unpublished');
    }

    public function hideImage(int $id): GalleryImage
    {
        return $this->updateImageStatus($id, 'hidden');
    }

    public function checkUserLikeStatus(int $galleryImageId, int $userId): bool
    {
        $image = GalleryImage::findOrFail($galleryImageId);
        return $image->likes()->where('user_id', $userId)->exists();
    }

    public function getUserGallery(int $userId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $query = GalleryImage::with('uploader', 'location')
            ->withCount('likes')
            ->where('user_id', $userId)
            ->orderByDesc('created_at');

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data to match frontend expectations
        $paginated->getCollection()->transform(function ($image) {
            return [
                'id' => $image->id,
                'file_path' => $image->file_path,
                'category' => $image->category,
                'location' => $image->location ? $image->location->name : null,
                'location_id' => $image->location_id,
                'description' => $image->description,
                'views' => $image->views,
                'likes_count' => $image->likes_count,
                'status' => $image->status,
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
