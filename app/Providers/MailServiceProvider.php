<?php

namespace App\Providers;

use App\Services\Contracts\IMailService;
use App\Services\MailServiceImpl;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
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
      $this->app->singleton(IMailService::class, MailServiceImpl::class);
    }
}
