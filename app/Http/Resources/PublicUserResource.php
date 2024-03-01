<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
                'id' => $this->id,
                'username' => $this->username,
                'detail_user' => [
                    'id' => $this->detailUser->id,
                    'fullname' => $this->detailUser->firstname . ' ' . $this->detailUser->lastname,
                    'country' => $this->detailUser->country,
                    'city' => $this->detailUser->city,
                    'avatar_url' => $this->detailUser->avatar_url,
                    'biography' => $this->detailUser->biography,
                    'social_media_links' => $this->detailUser->social_media_links,
                    'date_join' => Carbon::parse($this->created_at)->format('Y/m/d'),
                ],  
        ];
    }
}
