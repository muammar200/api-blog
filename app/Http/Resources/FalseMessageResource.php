<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FalseMessageResource extends JsonResource
{
    public $status;
    public $message;
    public $httpStatus;

    public function __construct($status, $message, $httpStatus)
    {
        $this->status = $status;
        $this->message = $message;
        $this->httpStatus = $httpStatus;
    }
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->httpStatus);
    }

    public static $wrap = null; // Menonaktifkan pembungkusan
}
