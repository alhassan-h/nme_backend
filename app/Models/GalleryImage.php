<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Location;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'category',
        'location_id',
        'description',
        'views',
        'user_id',
        'status',
    ];

    protected $casts = [
        'views' => 'integer',
        'status' => 'string',
    ];

    protected $attributes = [
        'views' => 0,
        'status' => 'pending',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(GalleryImageLike::class, 'gallery_image_id');
    }

    public function toggleLike(int $userId): bool
    {
        $existingLike = $this->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            return false; // Unliked
        } else {
            $this->likes()->create(['user_id' => $userId]);
            return true; // Liked
        }
    }

    public function incrementView(): void
    {
        $this->increment('views');
    }
}
