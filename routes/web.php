<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('throttle:' . \App\Providers\IdentityServiceProvider::THROTTLE_KEY)->group(function () {
    Route::get('/reset-password', [\App\Http\Controllers\Identity\PasswordController::class, 'resetPassword'])->name('password.reset');
});
