<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BusinessController;
use App\Http\Controllers\API\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    // Public routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [AuthController::class, 'user']);
    });
});

Route::prefix('category')->group(function () {
    Route::get('all', [CategoryController::class, 'index']);
});

Route::prefix('business')->group(function () {
    // Public routes
    Route::get('/', [BusinessController::class, 'index']);
    Route::get('{id}', [BusinessController::class, 'show']);

    // Protected routes (require login)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [BusinessController::class, 'store']);
        Route::post('{id}', [BusinessController::class, 'update']); // POST instead of PUT, for multipart file uploads
        Route::delete('{id}', [BusinessController::class, 'destroy']);
        Route::delete('images/{imageId}', [BusinessController::class, 'destroyImage']);
    });
});
