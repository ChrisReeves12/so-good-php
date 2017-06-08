<?php

namespace App\Providers;

use App\Services\Contracts\IRecordService;
use App\Services\RecordServiceImpl;
use Illuminate\Support\ServiceProvider;

class RecordServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
      $this->app->singleton(IRecordService::class, RecordServiceImpl::class);
    }
}
