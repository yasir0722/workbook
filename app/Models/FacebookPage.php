<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_page_id',
        'name',
        'username',
        'url',
        'category',
        'enabled',
        'sort_order',
        'last_fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'last_fetched_at' => 'datetime',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(FacebookPost::class);
    }
}
