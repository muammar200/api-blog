<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Js;
use Symfony\Component\HttpFoundation\Response;

class Guest
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization'); // Atau sesuaikan dengan cara Anda mendapatkan token
        if($token){
            return response()->json(['error' => 'You are already logged in.'], 403);
        }
        return $next($request);
    }

    // public function handle(Request $request, Closure $next): Response
    // {
    //     if(!auth()->user()->is_admin){
    //         return response()->json(['error' => 'Unauthorized access'], 403);
    //     }
    // }
}
