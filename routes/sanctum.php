<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

// Sanctum CSRF Cookie Route
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);