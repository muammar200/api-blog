<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Comment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $comment = Comment::findOrFail($request->id);
        
        if(auth()->user()->id !== $comment->user_id){
            return response()->json(['message' => 'data not found'], 404);
        }
        return $next($request);
    }
}
