<?php

use App\Http\Controllers\CommentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
