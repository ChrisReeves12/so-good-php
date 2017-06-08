<?php

namespace App\Providers;

use App\Services\Contracts\ISalesOrderService;
use App\Services\SalesOrderServiceImpl;
use Illuminate\Support\ServiceProvider;
use Validator;

class SalesOrderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
      Validator::extend('valid_payment_method', ISalesOrderService::class . '@validatePaymentMethod');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton(ISalesOrderService::class, SalesOrderServiceImpl::class);
    }
}
