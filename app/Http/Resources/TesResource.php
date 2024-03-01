<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TesResource extends ResourceCollection
{
    
    public function toArray(Request $request)
    {
        // return [
        //     'success' => $this->success,
        //     'message' => $this->message,
        //     // 'categories' => CategoryPostResource::collection($this->resource),
        //     // 'data' => [
        //     //     'id' => $this->id,
        //     //     'name' => $this->name,
        //     //     'slug' => $this->slug,
        //     // ],
        // ];
        return [
            // 'data' => $this->collection,
            // 'success' => $this->additional['success'],
            // 'message' => $this->additional['message'],

            'data' => $this->collection,
        'success' => $this->resource['success'] ?? true,
        'message' => $this->resource['message'] ?? 'Show All Category Successfully!',
        ];
        
    }
}
