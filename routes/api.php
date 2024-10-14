<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\LibrarianMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', LibrarianMiddleware::class])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/upload-picture', [UserController::class, 'uploadPicture']);

    Route::apiResource('books', BookController::class);
    Route::post('/books/{book}/images', [ImageController::class, 'store']);
    Route::post('/books/{book}/cover-image', [ImageController::class, 'updateCoverImage']);
    Route::post('books/{book}/discard', [BookController::class, 'discardBook'])->name('books.discard');

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('genres', GenreController::class);

    Route::apiResource('authors', AuthorController::class);

    Route::apiResource('publishers', PublisherController::class);

    Route::apiResource('policies', PolicyController::class)->only(['index', 'update']);

    Route::post('rentals/rent', [RentalController::class, 'rentBook'])->name('rentals.rent');
    Route::post('rentals/{rental}/return', [RentalController::class, 'returnBook'])->name('rentals.return');
    Route::get('/rentals/{status?}', [RentalController::class, 'getBooksByStatus'])
        ->whereIn('status', ['rented', 'returned', 'overdue'])->name('rentals.status');
    Route::get('/rentals/summary', [RentalController::class, 'getRentalSummary'])->name('rentals.summary');

});

Route::post('/password/reset-request', [PasswordResetController::class, 'sendResetPasswordEmail'])->name('password.reset-request');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
