<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Firebase\JWT\JWT;

class GoogleController extends Controller
{

    public function handleGoogleCallback()
    {
     try {
         $user = Socialite::driver('google')->user();

         $userExsited = User::where('oauth_id', $user->id)->where('oauth_type', 'google')->first();

         if($userExsited) {
             $payload = [
             'email' => $userExsited->email,
             'role' => $userExsited->role,
             'iat' => now()->timestamp,
             'exp' => now()->timestamp + 7200
             ];
             
            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

             return response()->json([
                     'msg' =>  'Login Success',
                     'token' => "Bearer {$jwt}"
             ],200 );
         }
         $newUser = User::create([
             'name' => $user->name,
             'email' => $user->email,
             'oauth_id' => $user->id,
             'ouath_type' => 'google',
             'password' => Hash::make($user->id)
         ]);
         
         $payload = [
             'email' => $newUser->email,
             'role' => $newUser->role,
             'iat' => now()->timestamp,
             'exp' => now()->timestamp + 7200
             ];
             
        $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
        return response()->json([
                 'msg' =>  'Login Success',
                 'token' => "Bearer {$jwt}"
         ],200 );

     }catch (Exception $e) {
         dd($e->getMessage());
     }
    }
}
