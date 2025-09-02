<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImageLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_image_id',
        'user_id',
    ];

    public function galleryImage(): BelongsTo
    {
        return $this->belongsTo(GalleryImage::class, 'gallery_image_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
