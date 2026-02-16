<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\Identity\AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [\App\Http\Controllers\Identity\AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/forgot-password', [\App\Http\Controllers\Identity\PasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [\App\Http\Controllers\Identity\PasswordController::class, 'resetPassword'])->middleware('throttle:resetPassword');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/me', function () {
        return auth()->user();
    });
});

/*
 * reset password form job should unique incelenecek
 * reset password request rate limit ve configler incelenecek
 * auth throttle süresi configler incelenecek
 * api responseları standartize olması için incelenecekk
 */
