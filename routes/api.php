<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MarketInsightController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductController;
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
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
});

// Newsletter subscription
Route::post('newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Public Product Routes
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');

// Authenticated product routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/{product}/favorite', [ProductController::class, 'toggleFavorite'])->name('products.toggleFavorite');
    Route::post('products/{product}/view', [ProductController::class, 'incrementView'])->name('products.incrementView');
});

// User dashboard
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('products', [UserController::class, 'products'])->name('user.products');
    Route::get('favorites', [UserController::class, 'favorites'])->name('user.favorites');
});

// Market Insights
Route::get('insights', [MarketInsightController::class, 'index'])->name('insights.index');
Route::get('insights/{id}', [MarketInsightController::class, 'show'])->name('insights.show');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('insights', [MarketInsightController::class, 'store'])->middleware('can:admin-access')->name('insights.store');
    Route::put('insights/{insight}', [MarketInsightController::class, 'update'])->middleware('can:admin-access')->name('insights.update');
    Route::delete('insights/{insight}', [MarketInsightController::class, 'destroy'])->middleware('can:admin-access')->name('insights.destroy');
});

// Forum
Route::get('forum/posts', [ForumController::class, 'index'])->name('forum.posts.index');
Route::get('forum/posts/{post}', [ForumController::class, 'show'])->name('forum.posts.show');
Route::get('forum/posts/{post}/replies', [ForumController::class, 'replies'])->name('forum.posts.replies');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('forum/posts', [ForumController::class, 'store'])->name('forum.posts.store');
    Route::post('forum/posts/{post}/replies', [ForumController::class, 'storeReply'])->name('forum.posts.replies.store');
});

// Gallery
Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::post('gallery/{id}/like', [GalleryController::class, 'toggleLike'])->name('gallery.toggleLike');
    Route::post('gallery/{id}/view', [GalleryController::class, 'incrementView'])->name('gallery.incrementView');
});

// Admin Routes with Admin middleware
Route::middleware(['auth:sanctum', 'can:admin-access'])->prefix('admin')->group(function () {
    Route::get('dashboard/stats', [AdminController::class, 'dashboardStats'])->name('admin.dashboard.stats');
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('users/{id}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.updateStatus');
    Route::get('products/pending', [AdminController::class, 'pendingProducts'])->name('admin.products.pending');
    Route::put('products/{id}/approve', [AdminController::class, 'approveProduct'])->name('admin.products.approve');
    Route::post('newsletters', [AdminController::class, 'createNewsletter'])->name('admin.newsletters.create');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');