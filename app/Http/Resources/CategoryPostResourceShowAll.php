<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CategoryPost;

class CategoryPostResourceShowAll extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
            ];
    }
}
