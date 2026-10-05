<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookPostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_type',
        'media_url',
        'thumbnail_url',
        'sort_order',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FacebookPost::class, 'facebook_post_id');
    }
}
