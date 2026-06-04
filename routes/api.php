<?php


use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BusinessController;
use App\Http\Controllers\API\CategoryController;

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
