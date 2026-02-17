<?php

use App\Http\Controllers\Identity\AuthController;
use App\Http\Controllers\Identity\PasswordController;
use App\Http\Controllers\Identity\VerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/forgot-password', [PasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->middleware('throttle:resetPassword');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    /* Verification */
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail']);
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
        ->middleware(['signed'])->name('verification.verify');

    Route::get('/me', function () {
        return auth()->user();
    });
});
