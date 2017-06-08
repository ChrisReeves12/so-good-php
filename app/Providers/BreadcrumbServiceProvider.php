<?php

namespace App\Providers;

use App\Services\Contracts\IBreadcrumbService;
use App\Services\BreadcrumbServiceImpl;
use Illuminate\Support\ServiceProvider;

class BreadcrumbServiceProvider extends ServiceProvider
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
        $this->app->singleton(IBreadcrumbService::class, BreadcrumbServiceImpl::class);
    }
}
