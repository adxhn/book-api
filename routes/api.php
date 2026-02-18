<?php

use App\Http\Controllers\Identity\AccountController;
use App\Http\Controllers\Identity\AuthController;
use App\Http\Controllers\Identity\PasswordController;
use App\Http\Controllers\Identity\VerificationController;
use Illuminate\Support\Facades\Route;

/* Authentication */
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/forgot-password', [PasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->middleware('throttle:resetPassword');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    /* Verification */
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->middleware('throttle:verificationEmail')->name('verification.email');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
        ->middleware(['signed'])->name('verification.verify');

    /* Account */
    Route::get('/sessions', [AccountController::class, 'sessions']);

    Route::get('/me', function () {
        return auth()->user();
    });
});

/**
 * too many attempts hatası türkçeleştirelecek
 * oturumlarla ilgili servis yazılacak, logout otma durumları, aktif sessionlar vs
 */
