<?php

namespace App\Providers;

use App\Services\Contracts\IMailListService;
use App\Services\MadMimiMailListServiceImpl;
use Illuminate\Support\ServiceProvider;

class MailListServiceProvider extends ServiceProvider
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
        $this->app->singleton(IMailListService::class, MadMimiMailListServiceImpl::class);
    }
}
