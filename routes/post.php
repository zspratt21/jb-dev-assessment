<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::patch('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::get('/posts/{id}/comments', [CommentController::class, 'indexByPost']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
});
