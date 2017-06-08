<?php

namespace App\Providers;

use App\Services\Contracts\IHomePageService;
use App\Services\HomePageServiceImpl;
use Illuminate\Support\ServiceProvider;

class HomePageServiceProvider extends ServiceProvider
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
        $this->app->singleton(IHomePageService::class, HomePageServiceImpl::class);
    }
}
