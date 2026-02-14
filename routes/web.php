<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reset-password-form', [\App\Http\Controllers\Identity\PasswordController::class, 'resetPasswordForm'])->name('password.reset');
