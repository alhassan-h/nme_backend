<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MarketInsightCategoryController;
use App\Http\Controllers\MarketInsightController;
use App\Http\Controllers\MineralCategoryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrganizationSettingController;
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
Route::get('newsletter/status', [NewsletterController::class, 'checkStatus'])->name('newsletter.status');
Route::post('newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Public organization setting status checks
Route::get('maintenance/status', [OrganizationSettingController::class, 'getMaintenanceStatus'])->name('maintenance.status');
Route::get('registration/status', [OrganizationSettingController::class, 'getRegistrationStatus'])->name('registration.status');
Route::get('marketplace/status', [OrganizationSettingController::class, 'getMarketplaceStatus'])->name('marketplace.status');
Route::get('newsletter/status', [OrganizationSettingController::class, 'getNewsletterStatus'])->name('newsletter.status');
Route::get('gallery/status', [OrganizationSettingController::class, 'getGalleryStatus'])->name('gallery.status');
Route::get('market-insights/status', [OrganizationSettingController::class, 'getMarketInsightsStatus'])->name('market-insights.status');
Route::get('community/status', [OrganizationSettingController::class, 'getCommunityStatus'])->name('community.status');

// Public organization profile
Route::get('organization-profile', [AdminController::class, 'getPublicOrganizationProfile'])->name('organization-profile.public');

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
    Route::get('gallery', [UserController::class, 'gallery'])->name('user.gallery');
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
    Route::get('user/liked-insights', [MarketInsightController::class, 'getLikedInsights'])->name('insights.liked');
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
Route::get('storage/images/gallery/{filename}', [GalleryController::class, 'serveImage'])->name('gallery.serve');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::post('gallery/{id}/like', [GalleryController::class, 'toggleLike'])->name('gallery.toggleLike');
    Route::get('gallery/{id}/like-status', [GalleryController::class, 'checkLikeStatus'])->name('gallery.likeStatus');
    Route::post('gallery/{id}/view', [GalleryController::class, 'incrementView'])->name('gallery.incrementView');
});

// Admin Gallery Routes
Route::middleware(['auth:sanctum', 'can:admin'])->prefix('admin/gallery')->group(function () {
    Route::get('/', [GalleryController::class, 'adminIndex'])->name('admin.gallery.index');
    Route::get('/{id}', [GalleryController::class, 'adminShow'])->name('admin.gallery.show');
    Route::put('/{id}', [GalleryController::class, 'update'])->name('admin.gallery.update');
    Route::put('/{id}/status', [GalleryController::class, 'updateStatus'])->name('admin.gallery.updateStatus');
    Route::put('/{id}/approve', [GalleryController::class, 'approve'])->name('admin.gallery.approve');
    Route::put('/{id}/publish', [GalleryController::class, 'publish'])->name('admin.gallery.publish');
    Route::put('/{id}/unpublish', [GalleryController::class, 'unpublish'])->name('admin.gallery.unpublish');
    Route::put('/{id}/hide', [GalleryController::class, 'hide'])->name('admin.gallery.hide');
    Route::delete('/{id}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
});

// Admin Routes with Admin middleware
Route::middleware(['auth:sanctum', 'can:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard/stats', [AdminController::class, 'dashboardStats'])->name('admin.dashboard.stats');
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::get('users/{user}/stats', [AdminController::class, 'getUserStats'])->name('admin.users.stats');
    Route::get('users/{user}/activity', [AdminController::class, 'getUserActivity'])->name('admin.users.activity');
    Route::get('users/{user}/login-history', [AdminController::class, 'getUserLoginHistory'])->name('admin.users.login-history');
    Route::get('users/{user}/login-history/{loginHistory}', [AdminController::class, 'getLoginHistory'])->name('admin.users.login-history.show');
    Route::put('users/{user}/login-history/{loginHistory}', [AdminController::class, 'updateLoginHistory'])->name('admin.users.login-history.update');
    Route::delete('users/{user}/login-history/{loginHistory}', [AdminController::class, 'deleteLoginHistory'])->name('admin.users.login-history.delete');
    Route::get('users/{user}/listings', [AdminController::class, 'getUserListings'])->name('admin.users.listings');
    Route::get('users/{user}/gallery', [AdminController::class, 'getUserGallery'])->name('admin.users.gallery');
    Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::put('users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.updateStatus');
    Route::post('users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.resetPassword');
    Route::post('users/{user}/avatar', [AdminController::class, 'uploadUserAvatar'])->name('admin.users.uploadAvatar');
    Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Admin Listings (Products) Management
    Route::get('listings', [AdminController::class, 'listings'])->name('admin.listings.index');
    Route::get('listings/value-breakdown', [AdminController::class, 'getListingsValueBreakdown'])->name('admin.listings.value-breakdown');
    Route::get('listings/{product}', [AdminController::class, 'showListing'])->name('admin.listings.show');
    Route::put('listings/{product}', [AdminController::class, 'updateListing'])->name('admin.listings.update');
    Route::delete('listings/{product}', [AdminController::class, 'deleteListing'])->name('admin.listings.delete');
    Route::put('listings/{product}/status', [AdminController::class, 'updateListingStatus'])->name('admin.listings.updateStatus');

    Route::get('products/pending', [AdminController::class, 'pendingProducts'])->name('admin.products.pending');
    Route::put('products/{id}/approve', [AdminController::class, 'approveProduct'])->name('admin.products.approve');
    Route::post('newsletters', [AdminController::class, 'createNewsletter'])->name('admin.newsletters.create');
    Route::get('newsletters', [AdminController::class, 'newsletters'])->name('admin.newsletters.index');
    Route::get('newsletters/{newsletter}', [AdminController::class, 'showNewsletter'])->name('admin.newsletters.show');
    Route::put('newsletters/{newsletter}', [AdminController::class, 'updateNewsletter'])->name('admin.newsletters.update');
    Route::delete('newsletters/{newsletter}', [AdminController::class, 'deleteNewsletter'])->name('admin.newsletters.delete');
    Route::post('newsletters/{newsletter}/send', [AdminController::class, 'sendNewsletter'])->name('admin.newsletters.send');
    Route::get('newsletter-subscribers', [AdminController::class, 'newsletterSubscribers'])->name('admin.newsletter-subscribers.index');
    Route::get('newsletter-stats', [AdminController::class, 'newsletterStats'])->name('admin.newsletter-stats');
    Route::get('recent-activity', [AdminController::class, 'recentActivity'])->name('admin.recent.activity');
    Route::get('pending-tasks', [AdminController::class, 'pendingTasks'])->name('admin.pending.tasks');

    // Insights management
    Route::get('insights', [AdminController::class, 'insights'])->name('admin.insights.index');
    Route::get('insights/{insight}', [AdminController::class, 'showInsight'])->name('admin.insights.show');
    Route::post('insights', [AdminController::class, 'createInsight'])->name('admin.insights.create');
    Route::put('insights/{insight}', [AdminController::class, 'updateInsight'])->name('admin.insights.update');
    Route::delete('insights/{insight}', [AdminController::class, 'deleteInsight'])->name('admin.insights.delete');
    Route::put('insights/{insight}/publish', [AdminController::class, 'publishInsight'])->name('admin.insights.publish');
    Route::put('insights/{insight}/unpublish', [AdminController::class, 'unpublishInsight'])->name('admin.insights.unpublish');
    Route::post('insights/bulk-publish', [AdminController::class, 'bulkPublishInsights'])->name('admin.insights.bulk-publish');
    Route::post('insights/bulk-unpublish', [AdminController::class, 'bulkUnpublishInsights'])->name('admin.insights.bulk-unpublish');
    Route::post('insights/bulk-delete', [AdminController::class, 'bulkDeleteInsights'])->name('admin.insights.bulk-delete');

    // Insight Categories management
    Route::get('insight-categories', [AdminController::class, 'insightCategories'])->name('admin.insight-categories.index');
    Route::get('insight-categories/{category}', [AdminController::class, 'showInsightCategory'])->name('admin.insight-categories.show');
    Route::post('insight-categories', [AdminController::class, 'createInsightCategory'])->name('admin.insight-categories.create');
    Route::put('insight-categories/{category}', [AdminController::class, 'updateInsightCategory'])->name('admin.insight-categories.update');
    Route::delete('insight-categories/{category}', [AdminController::class, 'deleteInsightCategory'])->name('admin.insight-categories.delete');

    // Units management
    Route::get('units', [UnitController::class, 'adminIndex'])->name('admin.units.index');
    Route::get('units/{unit}', [UnitController::class, 'show'])->name('admin.units.show');
    Route::post('units', [UnitController::class, 'store'])->name('admin.units.create');
    Route::put('units/{unit}', [UnitController::class, 'update'])->name('admin.units.update');
    Route::put('units/{unit}/toggle-active', [UnitController::class, 'toggleActive'])->name('admin.units.toggle-active');
    Route::delete('units/{unit}', [UnitController::class, 'destroy'])->name('admin.units.delete');

    // Locations management
    Route::get('locations', [LocationController::class, 'adminIndex'])->name('admin.locations.index');
    Route::get('locations/{location}', [LocationController::class, 'show'])->name('admin.locations.show');
    Route::post('locations', [LocationController::class, 'store'])->name('admin.locations.create');
    Route::put('locations/{location}', [LocationController::class, 'update'])->name('admin.locations.update');
    Route::put('locations/{location}/toggle-active', [LocationController::class, 'toggleActive'])->name('admin.locations.toggle-active');
    Route::delete('locations/{location}', [LocationController::class, 'destroy'])->name('admin.locations.delete');

    // Market Insight Categories management
    Route::get('market-insight-categories', [MarketInsightCategoryController::class, 'adminIndex'])->name('admin.market-insight-categories.index');
    Route::get('market-insight-categories/{market_insight_category}', [MarketInsightCategoryController::class, 'show'])->name('admin.market-insight-categories.show');
    Route::post('market-insight-categories', [MarketInsightCategoryController::class, 'store'])->name('admin.market-insight-categories.create');
    Route::put('market-insight-categories/{market_insight_category}', [MarketInsightCategoryController::class, 'update'])->name('admin.market-insight-categories.update');
    Route::put('market-insight-categories/{market_insight_category}/toggle-active', [MarketInsightCategoryController::class, 'toggleActive'])->name('admin.market-insight-categories.toggle-active');
    Route::delete('market-insight-categories/{market_insight_category}', [MarketInsightCategoryController::class, 'destroy'])->name('admin.market-insight-categories.delete');

    // Mineral Categories management
    Route::get('mineral-categories', [MineralCategoryController::class, 'adminIndex'])->name('admin.mineral-categories.index');
    Route::get('mineral-categories/{mineral_category}', [MineralCategoryController::class, 'show'])->name('admin.mineral-categories.show');
    Route::post('mineral-categories', [MineralCategoryController::class, 'store'])->name('admin.mineral-categories.create');
    Route::put('mineral-categories/{mineral_category}', [MineralCategoryController::class, 'update'])->name('admin.mineral-categories.update');
    Route::put('mineral-categories/{mineral_category}/toggle-active', [MineralCategoryController::class, 'toggleActive'])->name('admin.mineral-categories.toggle-active');
    Route::delete('mineral-categories/{mineral_category}', [MineralCategoryController::class, 'destroy'])->name('admin.mineral-categories.delete');

    // Organization Profile Settings
    Route::get('organization-profile', [AdminController::class, 'getOrganizationProfile'])->name('admin.organization-profile.show');
    Route::put('organization-profile', [AdminController::class, 'updateOrganizationProfile'])->name('admin.organization-profile.update');
    Route::post('organization-profile', [AdminController::class, 'createOrganizationProfileEntry'])->name('admin.organization-profile.create');
    Route::put('organization-profile/{profile}', [AdminController::class, 'updateOrganizationProfileEntry'])->name('admin.organization-profile.update-entry');
    Route::delete('organization-profile/{profile}', [AdminController::class, 'deleteOrganizationProfileEntry'])->name('admin.organization-profile.delete');

    // Business Settings
    Route::get('business-settings', [AdminController::class, 'getBusinessSettings'])->name('admin.business-settings.index');
    Route::post('business-settings', [AdminController::class, 'createBusinessSetting'])->name('admin.business-settings.create');
    Route::put('business-settings/{setting}', [AdminController::class, 'updateBusinessSetting'])->name('admin.business-settings.update');
    Route::delete('business-settings/{setting}', [AdminController::class, 'deleteBusinessSetting'])->name('admin.business-settings.delete');
    Route::get('business-settings/{key}/value', [AdminController::class, 'getBusinessSettingValue'])->name('admin.business-settings.value');
    Route::post('business-settings/bulk-update', [AdminController::class, 'bulkUpdateBusinessSettings'])->name('admin.business-settings.bulk-update');

    // Organization Settings
    Route::get('organization-settings', [AdminController::class, 'getOrganizationSettings'])->name('admin.organization-settings.index');
    Route::post('organization-settings', [AdminController::class, 'createOrganizationSetting'])->name('admin.organization-settings.create');
    Route::put('organization-settings/{setting}', [AdminController::class, 'updateOrganizationSetting'])->name('admin.organization-settings.update');
    Route::delete('organization-settings/{setting}', [AdminController::class, 'deleteOrganizationSetting'])->name('admin.organization-settings.delete');
    Route::get('organization-settings/{key}/value', [AdminController::class, 'getOrganizationSettingValue'])->name('admin.organization-settings.value');
    Route::get('organization-settings/type/{type}', [AdminController::class, 'getOrganizationSettingsByType'])->name('admin.organization-settings.by-type');
    Route::post('organization-settings/bulk-update', [AdminController::class, 'bulkUpdateOrganizationSettings'])->name('admin.organization-settings.bulk-update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
