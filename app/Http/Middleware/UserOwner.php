<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->username !== $request->route('user')->username){
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        return $next($request);
    }
}
