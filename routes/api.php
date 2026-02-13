<?php

use Illuminate\Support\Facades\Route;

Route::middleware('throttle:' . \App\Providers\IdentityServiceProvider::THROTTLE_KEY)->group(function () {
    Route::post('/register', [\App\Http\Controllers\Identity\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Identity\AuthController::class, 'login']);
});
