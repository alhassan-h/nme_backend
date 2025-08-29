<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING = 'pending';
    public const STATUS_SOLD = 'sold';

    protected $fillable = [
        'title',
        'description',
        'category',
        'price',
        'quantity',
        'unit',
        'location',
        'images',
        'seller_id',
        'status',
        'views',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'float',
        'views' => 'integer',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
        'views' => 0,
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', floatval($filters['min_price']));
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', floatval($filters['max_price']));
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        return $query;
    }

    public function toggleFavorite(User $user): bool
    {
        $exists = $this->favoritedBy()->where('user_id', $user->id)->exists();
        if ($exists) {
            $this->favoritedBy()->detach($user->id);
            return false;
        }
        $this->favoritedBy()->attach($user->id);
        return true;
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
