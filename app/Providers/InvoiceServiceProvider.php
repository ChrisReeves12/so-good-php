<?php

namespace App\Providers;

use App\Services\Contracts\IInvoiceService;
use App\Services\InvoiceServiceImpl;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
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
        $this->app->singleton(IInvoiceService::class, InvoiceServiceImpl::class);
    }
}
