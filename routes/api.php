<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MarketInsightController;
use App\Http\Controllers\MineralCategoryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the API']);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1')->name('auth.forgot-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    Route::get('login', function () {
        return response()->json(['message' => 'Authentication required'], 401);
    })->name('login');
});

// Newsletter subscription
Route::post('newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Mineral Categories
Route::get('mineral-categories', [MineralCategoryController::class, 'index'])->name('mineral-categories.index');

// Locations
Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('locations', [LocationController::class, 'store'])->middleware('can:admin')->name('locations.store');
    Route::put('locations/{id}', [LocationController::class, 'update'])->middleware('can:admin')->name('locations.update');
    Route::delete('locations/{id}', [LocationController::class, 'destroy'])->middleware('can:admin')->name('locations.destroy');
});

// Units
Route::get('units', [UnitController::class, 'index'])->name('units.index');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('units', [UnitController::class, 'store'])->middleware('can:admin')->name('units.store');
    Route::put('units/{id}', [UnitController::class, 'update'])->middleware('can:admin')->name('units.update');
    Route::delete('units/{id}', [UnitController::class, 'destroy'])->middleware('can:admin')->name('units.destroy');
});

// Public Product Routes
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');

// Authenticated product routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::post('products/{product}/favorite', [ProductController::class, 'toggleFavorite'])->name('products.toggleFavorite');
    Route::post('products/{product}/view', [ProductController::class, 'incrementView'])->name('products.incrementView');
});

// User dashboard
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('user')->group(function () {
    Route::get('products', [UserController::class, 'products'])->name('user.products');
    Route::get('favorites', [UserController::class, 'favorites'])->name('user.favorites');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('password', [UserController::class, 'changePassword'])->middleware('throttle:5,1')->name('user.password.change');
    Route::post('avatar', [UserController::class, 'uploadAvatar'])->name('user.avatar.upload');
    Route::post('send-verification-email', [UserController::class, 'sendEmailVerification'])->middleware('throttle:3,1')->name('user.send-verification-email');
    Route::post('verify-email', [UserController::class, 'verifyEmail'])->name('user.verify-email');
});

// Dashboard
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
    Route::get('stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('activity', [DashboardController::class, 'recentActivity'])->name('dashboard.activity');
    Route::get('revenue', [DashboardController::class, 'revenue'])->name('dashboard.revenue');
});

// Market Insights
Route::get('insights', [MarketInsightController::class, 'index'])->name('insights.index');
Route::get('insights/{id}', [MarketInsightController::class, 'show'])->name('insights.show');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('insights', [MarketInsightController::class, 'store'])->middleware('can:admin')->name('insights.store');
    Route::put('insights/{insight}', [MarketInsightController::class, 'update'])->middleware('can:admin')->name('insights.update');
    Route::delete('insights/{insight}', [MarketInsightController::class, 'destroy'])->middleware('can:admin')->name('insights.destroy');
    Route::post('insights/{id}/like', [MarketInsightController::class, 'toggleLike'])->name('insights.toggleLike');
});

// Forum
Route::get('forum/posts', [ForumController::class, 'index'])->name('forum.posts.index');
Route::get('forum/posts/{post}', [ForumController::class, 'show'])->name('forum.posts.show');
Route::get('forum/posts/{post}/replies', [ForumController::class, 'replies'])->name('forum.posts.replies');
Route::get('forum/stats', [ForumController::class, 'stats'])->name('forum.stats');
Route::get('forum/categories', [ForumController::class, 'categories'])->name('forum.categories');
Route::get('forum/top-contributors', [ForumController::class, 'topContributors'])->name('forum.top-contributors');
Route::post('forum/posts/{post}/views', [ForumController::class, 'incrementViews'])->name('forum.posts.increment-views');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('forum/posts', [ForumController::class, 'store'])->name('forum.posts.store');
    Route::post('forum/posts/{post}/replies', [ForumController::class, 'storeReply'])->name('forum.posts.replies.store');
});

// Gallery
Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('gallery/{id}', [GalleryController::class, 'show'])->name('gallery.show');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::post('gallery/{id}/like', [GalleryController::class, 'toggleLike'])->name('gallery.toggleLike');
    Route::post('gallery/{id}/view', [GalleryController::class, 'incrementView'])->name('gallery.incrementView');
});

// Admin Routes with Admin middleware
Route::middleware(['auth:sanctum', 'can:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard/stats', [AdminController::class, 'dashboardStats'])->name('admin.dashboard.stats');
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('users/{id}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.updateStatus');
    Route::get('products/pending', [AdminController::class, 'pendingProducts'])->name('admin.products.pending');
    Route::put('products/{id}/approve', [AdminController::class, 'approveProduct'])->name('admin.products.approve');
    Route::post('newsletters', [AdminController::class, 'createNewsletter'])->name('admin.newsletters.create');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
