<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{

    public function handleGoogleCallback()
    {
     try {
         $user = Socialite::driver('google')->user();

         $userExsited = User::where('oauth_id', $user->id)->where('oauth_type', 'google')->first();

         if($userExsited) {
             Auth::login($userExsited);

             return response()->json([
                     'msg' =>  'Login Success',
             ], );
         }
         $newUser = User::create([
             'name' => $user->name,
             'email' => $user->email,
             'oauth_id' => $user->id,
             'ouath_type' => 'google',
             'password' => Hash::make($user->id)
         ]);

         Auth::login($newUser);
         return response()->json([
                 'msg' =>  'Login Success',
         ], );

     }catch (Exception $e) {
         dd($e->getMessage());
     }
    }
}
