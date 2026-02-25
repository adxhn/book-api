<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\Identity\AccountController;
use App\Http\Controllers\Identity\AuthController;
use App\Http\Controllers\Identity\PasswordController;
use App\Http\Controllers\Identity\VerificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserBookController;
use Illuminate\Support\Facades\Route;

/* Authentication */
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/forgot-password', [PasswordController::class, 'sendResetMail']);
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->middleware('throttle:resetPassword');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    /* Verification */
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->middleware('throttle:verificationEmail')->name('verification.email');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
        ->middleware(['signed'])->name('verification.verify');

    /* Auth */
    Route::get('/sessions', [AuthController::class, 'sessions']);
    Route::post('/logout-other-devices', [AuthController::class, 'logoutOtherDevices']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /* Account */
    Route::put('/update-email', [AccountController::class, 'updateEmail']);
    Route::put('/change-password', [AccountController::class, 'changePassword']);

    /* Search */
    Route::get('/search', [SearchController::class, 'index']);

    /* Book */
    Route::get('/book/{slug}', [BookController::class, 'show']);

    /* User Book */
    Route::get('/user-book', [UserBookController::class, 'index']);
    Route::post('/user-book', [UserBookController::class, 'add']);
    Route::delete('/user-book/{slug}', [UserBookController::class, 'delete']);
});
