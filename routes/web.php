<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\GoogleController;

Route::get('oauth/register', function(){
    return Socialite::driver('google')->redirect();
});
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);