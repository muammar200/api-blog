<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->id,
            'author' => [
                'id' => $this->author_id,
                'username' => $this->author->username,
                'name' => $this->author->DetailUser->firstname . $this->author->DetailUser->lastname,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            // 'category' => [
            //     'id' => $this->category ? $this->category->id : null,
            //     'name' => $this->category ? $this->category->name : null,
            // ],
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'published_at' => Carbon::parse($this->published_at)->format('Y/m/d H:i:s'),
            'post_image' => $this->postImages->pluck('image')->toArray(),
            'comments' => [
                'total_comments' => $this->comments->count(),
                'comment_list' => $this->comments->sortByDesc('created_at')->map(function ($comment){
                    return [
                        'id'=> $comment->id,
                        'user_id'=> $comment->user_id,
                        'post_id'=> $comment->post_id,
                        'comments_content'=> $comment->comments_content,
                        'created_at'=> Carbon::parse($comment->created_at)->format('Y/m/d H:i:s'),
                        'updated_at'=> Carbon::parse($comment->updated_at)->format('Y/m/d H:i:s'),
                        'commentator' => [
                            'id' => $comment->commentator->id,
                            'username' => $comment->commentator->username,
                            'fullname' => $comment->commentator->detailUser->firstname . ' ' . $comment->commentator->detailUser->lastname
                        ],
                    ];
                }),
            ],
            'likes' => [
                'total_likes' => $this->likes->count(),
                'like_list' => $this->likes->sortByDesc('created_at')->map(function ($like){
                    return [
                        'id' => $like->id,
                        'user_id' => $like->user->id,
                        'username' => $like->user->username,
                        'fullname' => $like->user->detailUser->firstname . ' ' . $like->user->detailUser->lastname,
                    ];
                })
            ]
        ];
    }
}
