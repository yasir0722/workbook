<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacebookPageResource;
use App\Models\FacebookPage;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = $request->integer('per_page', 4);
        $perPage = min(max($perPage, 1), 20);

        $pages = FacebookPage::query()
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with([
                'posts' => fn ($query) => $query
                    ->with('media')
                    ->orderByDesc('published_at')
                    ->limit($perPage),
            ])
            ->get();

        return FacebookPageResource::collection($pages);
    }
}
