<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PublicUserController;
use App\Http\Controllers\CategoryPostController;
use App\Http\Controllers\DashboardPostController;
use App\Http\Controllers\LikeController;

// Authentication
Route::middleware(['guest'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Verify Email
Route::get('email/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [AuthController::class, 'resend'])->name('verification.resend')->middleware(['auth:api']);

// Route::middleware(['auth:sanctum'])->group(function () {
Route::middleware(['auth:api', 'verified'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Refresh Token
    Route::post('/refresh-token', [AuthController::class, 'refresh']);

    // User Data Routes
    Route::prefix('user')->group(function () {
        Route::get('/{user:username}', [UserController::class, 'show'])->middleware(['user-owner']);
        Route::put('/{user:username}', [UserController::class, 'update'])->middleware(['user-owner']);
        Route::post('/{user:username}', [UserController::class, 'update'])->middleware(['user-owner']);
        Route::delete('/{user:username}', [UserController::class, 'destroy'])->middleware(['user-owner']);
    });

    // Admin - Category Routes
    Route::prefix('admin/categories')->middleware(['admin'])->group(function () {
        Route::get('/', [CategoryPostController::class, 'index']);
        Route::get('/deleted', [CategoryPostController::class, 'showAllDeleted']);
        Route::get('/deleted/{id}', [CategoryPostController::class, 'showSingleDeleted']);
        Route::post('/{id}/restore', [CategoryPostController::class, 'restore']);
        Route::get('/{id}', [CategoryPostController::class, 'show']);
        Route::post('/', [CategoryPostController::class, 'store']);
        Route::put('/{id}', [CategoryPostController::class, 'update']);
        Route::delete('/{id}', [CategoryPostController::class, 'destroy']);
    });

    // User - Dashboard Routes
    Route::prefix('{user:username}/dashboard')->middleware(['user-owner', 'verified'])->group(function () {
        Route::get('/', [DashboardPostController::class, 'index']);
        Route::get('/posts/{post:slug}', [DashboardPostController::class, 'show']);
        Route::post('/posts', [DashboardPostController::class, 'store']);
        // Route::put('/posts/{post:slug}', [DashboardPostController::class, 'update']);
        Route::post('/posts/{post:slug}', [DashboardPostController::class, 'update']);
        Route::delete('/posts/{post:slug}', [DashboardPostController::class, 'destroy']);
        Route::get('/deleted/posts', [DashboardPostController::class, 'showAllDeleted']);
        Route::get('/posts/deleted/{post:slug}', [DashboardPostController::class, 'showSingleDeleted']);
        Route::put('/posts/{post:slug}/restore', [DashboardPostController::class, 'restore']);
    });

    // Comment Routes
    Route::middleware(['verified'])->group(function () {
        Route::post('/comment', [CommentController::class, 'store']);
        Route::put('/comment/{id}', [CommentController::class, 'update'])->middleware(['comment-owner']);
        Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->middleware(['comment-owner']);
    });;

    // Like Post Routes
    Route::post('/posts/{post:slug}/like', [LikeController::class, 'toggleLike'])->middleware('verified');
});

// Public Posts
Route::get('/posts', [PostController::class, 'index']);
Route::get('posts/{post:slug}', [PostController::class, 'show']);

// Public Users
Route::get('/{user:username}', [PublicUserController::class, 'show']);
