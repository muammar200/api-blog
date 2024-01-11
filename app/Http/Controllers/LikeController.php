<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\LikeResource;
use App\Http\Resources\CommentResource;

class LikeController extends Controller
{
    public function toggleLike(Post $post)
    {
        $user = auth()->user();
        
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
