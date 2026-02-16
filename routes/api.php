<?php

use Illuminate\Support\Facades\Route;

Route::middleware('throttle:' . \App\Providers\IdentityServiceProvider::THROTTLE_KEY)->group(function () {
    Route::post('/register', [\App\Http\Controllers\Identity\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Identity\AuthController::class, 'login']);
    Route::post('/forgot-password', [\App\Http\Controllers\Identity\PasswordController::class, 'forgotPassword']);
    Route::post('/reset-password', [\App\Http\Controllers\Identity\PasswordController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/me', function () {
        return auth()->user();
    });
});

/*
 * geminiye auth ile ilgili throtlle yapılandırması sorulacak
 * reset password form job should unique incelenecek
 * reset password request rate limit ve configler incelenecek
 * auth throttle süresi configler incelenecek
 * api responseları standartize olması için incelenecek
 * şifre değiştirildiğinde logout olma durumuna bakılacak
 */
