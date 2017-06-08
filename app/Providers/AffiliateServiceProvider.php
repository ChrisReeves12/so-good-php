<?php

namespace App\Providers;

use App\Services\Contracts\IAffiliateService;
use App\Services\AffiliateServiceImpl;
use Illuminate\Support\ServiceProvider;

class AffiliateServiceProvider extends ServiceProvider
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
        $this->app->singleton(IAffiliateService::class, AffiliateServiceImpl::class);
    }
}
