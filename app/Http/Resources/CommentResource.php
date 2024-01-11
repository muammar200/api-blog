<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public $success;
    public $message;
    
    public function __construct($success, $message, $resource)
    {
        $this->success = $success;
        $this->message = $message;
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'post_id' => $this->post_id,
                'comments_content' => $this->comments_content,
                'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
                'commentator' => [
                    'id' => $this->commentator->id,
                    'username' => $this->commentator->username,
                    'fullname' => $this->commentator->detailUser->firstname . ' ' . $this->commentator->detailUser->lastname
                ],
            ]
        ];
    }
}
