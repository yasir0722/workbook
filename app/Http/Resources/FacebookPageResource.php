<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacebookPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'facebook_page_id' => $this->facebook_page_id,
            'name' => $this->name,
            'username' => $this->username,
            'url' => $this->url,
            'category' => $this->category,
            'enabled' => $this->enabled,
            'sort_order' => $this->sort_order,
            'last_fetched_at' => $this->last_fetched_at?->toISOString(),
            'posts' => FacebookPostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
