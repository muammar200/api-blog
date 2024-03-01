<?php

namespace App\Http\Controllers\API;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LikeResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;


class LikeController extends Controller
{
    public function like(Post $post)
    {
        $user = Auth::user();
        
        if($user->likes->where('post_id', $post->id)->isNotEmpty()){
            $user->likes()->where('post_id', $post->id)->delete();
            return new LikeResource(true, 'Like removed', $user, $post);
        } else {
            $comment = Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id
            ]);
            return new LikeResource(true, 'Post liked', $user, $post);
        }
    }
}
