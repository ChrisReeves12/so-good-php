<?php

namespace App\Providers;

use App\Services\Contracts\IPayPalService;
use App\Services\PayPalServiceImpl;
use Illuminate\Support\ServiceProvider;

class PayPalServiceProvider extends ServiceProvider
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
        $this->app->singleton(IPayPalService::class, PayPalServiceImpl::class);
    }
}
