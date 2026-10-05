<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacebookPageRequest;
use App\Http\Requests\UpdateFacebookPageRequest;
use App\Http\Resources\FacebookPageResource;
use App\Models\FacebookPage;
use Illuminate\Http\Response;

class FacebookPageController extends Controller
{
    public function index()
    {
        return FacebookPageResource::collection(
            FacebookPage::query()->orderBy('sort_order')->orderBy('name')->get(),
        );
    }

    public function store(StoreFacebookPageRequest $request)
    {
        $attributes = $request->validated();
        $attributes['enabled'] ??= true;
        $attributes['sort_order'] ??= ((int) FacebookPage::query()->max('sort_order')) + 1;

        $page = FacebookPage::query()->create($attributes);

        return (new FacebookPageResource($page))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(FacebookPage $page)
    {
        return new FacebookPageResource($page);
    }

    public function update(UpdateFacebookPageRequest $request, FacebookPage $page)
    {
        $page->update($request->validated());

        return new FacebookPageResource($page->fresh());
    }

    public function destroy(FacebookPage $page): Response
    {
        $page->delete();

        return response()->noContent();
    }
}
