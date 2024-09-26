<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);

Route::post('users/{user}/upload-picture', [UserController::class, 'uploadPicture']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
