<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Category routes
    Route::apiResource('categories', CategoryController::class);

    // Post routes
    Route::apiResource('posts', PostController::class);
    Route::post('posts/{post}/publish', [PostController::class, 'publish']);
    Route::post('posts/{post}/unpublish', [PostController::class, 'unpublish']);

    // Comment routes
    Route::apiResource('comments', CommentController::class);
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve']);
    Route::post('comments/{comment}/reject', [CommentController::class, 'reject']);
}); 