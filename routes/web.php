<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

/* Social Auth */
Route::get('/social/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/social/callback', function () {
    $user = Socialite::driver('google')->user();
    dd($user);
});
