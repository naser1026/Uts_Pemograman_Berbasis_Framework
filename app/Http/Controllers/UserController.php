<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);


        if($validator->fails()){
             return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        if(Auth::attempt($validated)) {

            $payload = [
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp +7200,
            ];

            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

            return response()->json([
                "data" => [
                    'msg' => 'Login successful',
                    'token' => "Bearer {$jwt}"
                ]
            ], 200);
        }
        return response()->json([
                'msg' =>  'Login failed, email or password incorrect'
        ], 400);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'email' => "required|email|unique:users",
            'password' => "required"
        ]);

        if($validator->fails()){
             return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);
        return response()->json([
            "data" => [
                'msg' => 'Registration successful'
            ]
        ]);
    }
}
