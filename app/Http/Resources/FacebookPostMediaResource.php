<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacebookPostMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'media_type' => $this->media_type,
            'media_url' => $this->media_url,
            'thumbnail_url' => $this->thumbnail_url,
            'sort_order' => $this->sort_order,
        ];
    }
}
