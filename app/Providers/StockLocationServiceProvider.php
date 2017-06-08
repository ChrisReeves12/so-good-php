<?php

namespace App\Providers;

use App\Services\Contracts\IStockLocationService;
use App\Services\StockLocationServiceImpl;
use Illuminate\Support\ServiceProvider;

class StockLocationServiceProvider extends ServiceProvider
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
        $this->app->singleton(IStockLocationService::class, StockLocationServiceImpl::class);
    }
}
