<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public $status;
    public $message;
    public $resource;

    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => [
                'id' => $this->id,
                'username' => $this->username,
                'email' => $this->email,
                'email_verified_at' => $this->email_verified_at,
                'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('Y/m/d H:i:s') : null,
                'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y/m/d H:i:s') : null,
                'detail_user' => $this->detailUser,
            ],
        ];
    }

    // public function toResponse($request)
    // {
    //     return parent::toResponse($request)->setStatusCode($this->httpCode);
    // }
}
