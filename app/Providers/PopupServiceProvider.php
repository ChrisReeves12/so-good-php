<?php

namespace App\Providers;

use App\Services\Contracts\IPopupService;
use App\Services\PopupServiceImpl;
use Illuminate\Support\ServiceProvider;

class PopupServiceProvider extends ServiceProvider
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
        $this->app->singleton(IPopupService::class, PopupServiceImpl::class);
    }
}
