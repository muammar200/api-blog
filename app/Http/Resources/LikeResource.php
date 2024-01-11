<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    public $success;
    public $message;
    public $currentUser;
    public $currentPost;

    public function __construct($success, $message, $user, $post)
    {
        $this->success = $success;
        $this->message = $message;
        $this->currentUser = $user;
        $this->currentPost = $post;
    }

    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => [
                'user' => [
                    'id' => $this->currentUser->id,
                    'username' => $this->currentUser->username,
                    'fullname' => $this->currentUser->detailUser->firstname . ' ' . $this->currentUser->detailUser->lastname,
                ],   
                'post' => [
                    'id' => $this->currentPost->id,
                    'title' => $this->currentPost->title,
                    'slug' => $this->currentPost->slug,
                ]
            ]
        ];
    }
}
