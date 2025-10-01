<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\CloudinaryService;

Route::get('storage/{path}', function ($path) {
    return Storage::disk('public')->response($path);
})->where('path', '.*');

// Test route for Cloudinary integration
Route::get('/cloudinary-test', function () {
    try {
        $service = app(CloudinaryService::class);
        return response()->json([
            'message' => 'Cloudinary service initialized successfully',
            'cloud_name' => config('services.cloudinary.cloud_name')
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
