<?php

use App\Http\Controllers\Api\FacebookPageController;
use App\Http\Controllers\Api\FacebookPagePostController;
use App\Http\Controllers\Api\FeedController;
use Illuminate\Support\Facades\Route;

Route::get('feed', FeedController::class);
Route::apiResource('pages', FacebookPageController::class);
Route::get('pages/{page}/posts', [FacebookPagePostController::class, 'index']);
