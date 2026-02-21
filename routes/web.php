<?php

use App\Http\Controllers\Identity\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

/* Social Auth */
Route::get('/social/redirect/{provider}', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/social/callback/{provider}', function () {
    $user = Socialite::driver('google')->user();
    dd($user);
});

Route::get('/social/callback/{provider}', [AuthController::class, 'socialAuth']);
