<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacebookPostResource;
use App\Models\FacebookPage;
use Illuminate\Http\Request;

class FacebookPagePostController extends Controller
{
    public function index(Request $request, FacebookPage $page)
    {
        $perPage = min(max($request->integer('per_page', 4), 1), 20);

        return FacebookPostResource::collection(
            $page->posts()
                ->with('media')
                ->orderByDesc('published_at')
                ->paginate($perPage),
        );
    }
}
