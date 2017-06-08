<?php

namespace App\Providers;

use App\Services\Contracts\IGiftCardService;
use App\Services\GiftCardServiceImpl;
use Illuminate\Support\ServiceProvider;

class GiftCardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IGiftCardService::class, GiftCardServiceImpl::class);
    }
}
