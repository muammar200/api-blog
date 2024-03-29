<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category->name,
            'content' => $this->content,
            'created_at' => Carbon::parse($this->created_at)->format('Y/m/d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y/m/d H:i:s'),
            'published_at' => $this->published_at ? Carbon::parse($this->published_at)->format('Y/m/d H:i:s') : null,
            'deleted_at' => $this->deleted_at ? Carbon::parse($this->deleted_at)->format('Y/m/d H:i:s') : null,
            'post_image' => $this->postImages->pluck('image')->toArray(), // Mengambil hanya nama file gambar dari postImages
        ];
    }
}
