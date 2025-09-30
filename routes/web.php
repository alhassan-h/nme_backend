<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('storage/{path}', function ($path) {
    return Storage::disk('public')->response($path);
})->where('path', '.*');
