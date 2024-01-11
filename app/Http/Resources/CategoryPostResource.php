<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPostResource extends JsonResource
{
    public $success;
    public $message;
    public $httpCode;

    public function __construct($resource, $success, $message)
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
        // $this->httpCode = $httpCode;
    }

    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                // 'deleted_at' => $this->deleted_at
            ],
        ];
    }

    // public function toResponse($request)
    // {
    //     return parent::toResponse($request)->setStatusCode($this->httpCode);
    // }
}
