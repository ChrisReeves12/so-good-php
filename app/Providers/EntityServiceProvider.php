<?php

namespace App\Providers;

use App\Services\Contracts\IEntityService;
use Illuminate\Support\ServiceProvider;
use App\Services\EntityServiceImpl;

class EntityServiceProvider extends ServiceProvider
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
      $this->app->singleton(IEntityService::class, EntityServiceImpl::class);
    }
}
