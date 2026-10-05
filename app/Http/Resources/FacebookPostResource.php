<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacebookPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'facebook_post_id' => $this->facebook_post_id,
            'message' => $this->message,
            'permalink' => $this->permalink,
            'published_at' => $this->published_at?->toISOString(),
            'media' => FacebookPostMediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
