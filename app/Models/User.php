<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_BUYER = 'buyer';
    public const ROLE_SELLER = 'seller';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_BOTH = 'both';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_type',
        'company',
        'phone',
        'location',
        'verified',
        'status',
        'first_name',
        'last_name',
        'bio',
        'website',
        'avatar',
        'last_login_at',
    ];

    protected $appends = [
        'userType',
        'firstName',
        'lastName',
        'name',
        'bio',
        'website',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'user_type' => 'buyer',
        'verified' => false,
        'status' => 'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function marketInsights()
    {
        return $this->hasMany(MarketInsight::class);
    }

    public function marketInsightLikes()
    {
        return $this->hasMany(MarketInsightLike::class);
    }

    public function loginHistory()
    {
        return $this->hasMany(UserLoginHistory::class);
    }

    public function getUserTypeAttribute()
    {
        return $this->attributes['user_type'];
    }

    public function isAdmin(): bool
    {
        return $this->user_type === self::ROLE_ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->user_type === self::ROLE_SELLER;
    }

    public function isBuyer(): bool
    {
        return $this->user_type === self::ROLE_BUYER;
    }

    public function isBoth(): bool
    {
        return $this->user_type === self::ROLE_BOTH;
    }

    /**
     * Get the first name attribute.
     */
    public function getFirstNameAttribute()
    {
        return $this->attributes['first_name'] ?? '';
    }

    /**
     * Get the last name attribute.
     */
    public function getLastNameAttribute()
    {
        return $this->attributes['last_name'] ?? '';
    }

    /**
     * Get the bio attribute.
     */
    public function getBioAttribute()
    {
        return $this->attributes['bio'] ?? '';
    }

    /**
     * Get the website attribute.
     */
    public function getWebsiteAttribute()
    {
        return $this->attributes['website'] ?? '';
    }

    /**
     * Get the name attribute.
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the avatar attribute.
     */
    public function getAvatarAttribute()
    {
        return $this->attributes['avatar'] ?? '';
    }
}
