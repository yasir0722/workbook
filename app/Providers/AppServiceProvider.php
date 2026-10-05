<?php

namespace App\Providers;

use App\Contracts\ContentSourceInterface;
use App\Services\MockFacebookContentSource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ContentSourceInterface::class, MockFacebookContentSource::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
