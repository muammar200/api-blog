<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\EmailController;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CategoryPostController;
use App\Http\Controllers\API\PublicUserController;  
use App\Http\Controllers\API\DashboardPostController;

// Authentication
Route::middleware(['guest'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});

// Verify Email
Route::get('/email/verify/{id}', [EmailController::class, 'verify'])->name('verification.verify');
Route::get('/email/resend', [EmailController::class, 'resend'])->name('verification.resend')->middleware(['auth:api']);
// Route::get('/email-verified', [AuthController::class, 'emailVerified'])->name('email.verified');

// User Password
Route::patch('/change-password', [PasswordController::class, 'changePassword'])->middleware(['auth:api']);
Route::middleware(['guest'])->group(function () {
    Route::post('/forgot-password', [PasswordController::class, 'sendEmailForgotPassword']);
    Route::get('/reset-password/{token}', [PasswordController::class, 'getTokenResetPassword'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'resetPassword']);
});

// User Data Routes
Route::middleware(['auth:api', 'user-owner'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/{user:username}', [UserController::class, 'show']);
        Route::put('/{user:username}', [UserController::class, 'update']);
        // Route::post('/{user:username}', [UserController::class, 'update']);
        Route::delete('/{user:username}', [UserController::class, 'destroy']);
    });
});

// Admin - Category Routes
Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::prefix('/admin/categories')->group(function () {
        Route::get('/', [CategoryPostController::class, 'index']);
        Route::get('/{id}', [CategoryPostController::class, 'show']);
        Route::post('/', [CategoryPostController::class, 'store']);
        Route::patch('/{id}', [CategoryPostController::class, 'update']);
        Route::delete('/{id}', [CategoryPostController::class, 'destroy']);
    });
});

// User - Dashboard Routes
// Route::middleware(['auth:sanctum'])->group(function () {
Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::prefix('{user:username}/dashboard')->group(function () {
        Route::get('/', [DashboardPostController::class, 'index']);
        Route::get('/posts/{post:slug}', [DashboardPostController::class, 'show']);
        Route::post('/posts', [DashboardPostController::class, 'store']);
        Route::put('/posts/{post:slug}', [DashboardPostController::class, 'update']);
        // Route::post('/posts/{post:slug}', [DashboardPostController::class, 'update']);
        Route::delete('/posts/{post:slug}', [DashboardPostController::class, 'destroy']);
        Route::get('/deleted/posts', [DashboardPostController::class, 'showAllDeleted']);
        Route::get('/posts/deleted/{post:slug}', [DashboardPostController::class, 'showSingleDeleted']);
        Route::put('/posts/{post:slug}/restore', [DashboardPostController::class, 'restore']);
    });
});

// User - Like & Comment
Route::middleware(['auth:api', 'verified'])->group(function () {
    // Like Post Routes
    Route::post('/posts/{post:slug}/like', [LikeController::class, 'like']);
    
    // Comment Routes
    Route::post('/comment', [CommentController::class, 'store']);
    Route::put('/comment/{id}', [CommentController::class, 'update'])->middleware(['comment-owner']);
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->middleware(['comment-owner']);
});

// Public Access Routes 
// Public Categories
Route::get('/public/categories', [CategoryPostController::class, 'index']);

// Public Posts
Route::get('/public/posts', [PostController::class, 'index']);
Route::get('/public/posts/{post:slug}', [PostController::class, 'show']);

// Public Users
Route::get('public/users/search', [PublicUserController::class, 'search']);
Route::get('public/users/search/{user:username}', [PublicUserController::class, 'show']);

