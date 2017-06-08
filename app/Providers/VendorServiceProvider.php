<?php

namespace App\Providers;

use App\Services\Contracts\IVendorService;
use App\Services\VendorServiceImpl;
use Illuminate\Support\ServiceProvider;

class VendorServiceProvider extends ServiceProvider
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
        $this->app->singleton(IVendorService::class, VendorServiceImpl::class);
    }
}
