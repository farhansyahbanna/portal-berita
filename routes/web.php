<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\PostController as AdminPostController;
use App\Http\Controllers\Api\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

use App\Http\Controllers\Api\Editor\PostController as EditorPostController;
use App\Http\Controllers\Api\Editor\DashboardController as EditorDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VueController;
use Illuminate\Support\Facades\Route;


// Public API Routes
Route::get('/', [VueController::class, 'index'])->name('home');
Route::get('/posts', [VueController::class, 'index'])->name('posts.index');
Route::get('/posts/{id}', [VueController::class, 'showPost'])->name('posts.show');
Route::get('/login', [VueController::class, 'login'])->name('login');
Route::get('/forgot-password', [VueController::class, 'index'])->name('password.request');
Route::get('/reset-password', [VueController::class, 'index'])->name('password.reset');

Route::prefix('api')->group(function () {
    // Auth Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/user', [AuthController::class, 'user']);

    // Post Routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);

    // Comment Routes
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');

    // Admin Routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/admin/dashboard/stats', [DashboardController::class, 'stats']);

        Route::get('/admin/posts', [AdminPostController::class, 'index']);   
        Route::post('/admin/posts', [AdminPostController::class, 'store']);
        Route::put('/admin/posts/{id}', [AdminPostController::class, 'update']);
        Route::delete('/admin/posts/{id}', [AdminPostController::class, 'destroy']);
        Route::get('/admin/posts/recent', [AdminPostController::class, 'recent']);
        Route::get('/admin/posts/{id}', [AdminPostController::class, 'show']);

        Route::get('/admin/comments/recent', [AdminCommentController::class, 'recents']);
        Route::get('/admin/comments', [AdminCommentController::class, 'index']);
        Route::put('/admin/comments/{id}/status', [AdminCommentController::class, 'updateStatus']);
        Route::delete('/admin/comments/{id}', [AdminCommentController::class, 'destroy']);
        Route::post('/admin//comments/bulk-action', [AdminCommentController::class, 'bulkAction']);
        Route::get('/admin//comments/recent', [AdminCommentController::class, 'recent']);

        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users', [AdminUserController::class, 'store']);
        Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
        Route::put('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']);
        Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
    });

    // Editor Routes
    Route::middleware(['auth:sanctum', 'editor'])->group(function () {
        Route::get('/editor/dashboard/stats', [EditorDashboardController::class, 'stats']);
        Route::get('/editor/dashboard/posts/recent', [EditorDashboardController::class, 'recentPosts']);
        Route::get('/editor/dashboard/activity/recent', [EditorDashboardController::class, 'recentActivity']);

        Route::get('/editor/posts', [EditorPostController::class, 'index']);
        Route::post('/editor/posts', [EditorPostController::class, 'store']);
        Route::get('/editor/posts/{id}', [EditorPostController::class, 'show']);
        Route::put('/editor/posts/{id}', [EditorPostController::class, 'update']);
        Route::delete('/editor/posts/{id}', [EditorPostController::class, 'destroy']);
    });
});

Route::get('{any}', function () {
    return view('app'); 
})->where('any', '.*');


