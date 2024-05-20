<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('oauth/register', function(){
    return Socialite::driver('google')->redirect();
});
