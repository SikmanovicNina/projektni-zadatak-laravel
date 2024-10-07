<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\LibrarianMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', LibrarianMiddleware::class])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/upload-picture', [UserController::class, 'uploadPicture']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('genres', GenreController::class);
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('publishers', PublisherController::class);

});

Route::post('/password/reset-request', [PasswordResetController::class, 'sendResetPasswordEmail'])->name('password.reset-request');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
