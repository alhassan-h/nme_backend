<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'category',
        'location',
        'description',
        'views',
        'likes_count',
        'user_id',
    ];

    protected $casts = [
        'views' => 'integer',
        'likes_count' => 'integer',
    ];

    protected $attributes = [
        'views' => 0,
        'likes_count' => 0,
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toggleLike(int $userId): bool
    {
        $liked = \DB::table('gallery_image_likes')
            ->where('gallery_image_id', $this->id)
            ->where('user_id', $userId)
            ->exists();

        if ($liked) {
            \DB::table('gallery_image_likes')
                ->where('gallery_image_id', $this->id)
                ->where('user_id', $userId)
                ->delete();
            $this->decrement('likes_count');
            return false;
        } else {
            \DB::table('gallery_image_likes')->insert([
                'gallery_image_id' => $this->id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->increment('likes_count');
            return true;
        }
    }

    public function incrementView(): void
    {
        $this->increment('views');
    }
}
