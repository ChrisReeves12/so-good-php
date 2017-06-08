<?php

namespace App\Providers;

use App\Services\Contracts\IMailService;
use App\Services\Contracts\ISubscriptionService;
use App\Services\SubscriptionServiceImpl;
use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
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
      $this->app->singleton(ISubscriptionService::class, SubscriptionServiceImpl::class);
    }
}
