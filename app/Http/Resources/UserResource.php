<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public $success;
    public $message;
    public $httpCode;
    public $tokenUser;

    public function __construct($resource, $tokenUser, $success, $message)
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
        $this->tokenUser = $tokenUser;
        // $this->httpCode = $httpCode;
    }

    public function toArray(Request $request): array
{
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => [
                'id' => $this->id,
                'username' => $this->username,
                'email' => $this->email,
                'email_verified_at' => $this->email_verified_at,
                'created_at' => Carbon::parse($this->created_at)->format('Y/m/d H:i:s'),
                'detail_user' => $this->detailUser
            ],
            'token_user' => $this->tokenUser,
        ];
    }

    // public function toResponse($request)
    // {
    //     return parent::toResponse($request)->setStatusCode($this->httpCode);
    // }
}
