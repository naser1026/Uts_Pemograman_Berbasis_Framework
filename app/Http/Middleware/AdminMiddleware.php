<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            $jwt = $request->bearerToken();

            if($jwt == null || $jwt == '')
            {
                return response()->json([
                        'msg' =>  'Token must be filled in'
                ], 401);
            }

            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));

            if($decoded->role == 'admin')
            {
                return $next($request);
            }
            return response()->json([
                    'msg' =>  'Unauthorized'
            ], 401);
        }catch(ExpiredException $e) {
            return response()->json($e->getMessage(), 401);
        }

    }
}
