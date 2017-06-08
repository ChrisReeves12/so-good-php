<?php

namespace App\Providers;

use App\Services\Contracts\INoSQLDataSourceService;
use App\Services\SolrDataSourceService;
use Illuminate\Support\ServiceProvider;

class NoSQLDataSourceServiceProvider extends ServiceProvider
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
      $this->app->singleton(INoSQLDataSourceService::class, SolrDataSourceService::class);
    }
}
