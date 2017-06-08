<?php

namespace App\Providers;

use App\Services\Contracts\IProductService;
use App\Services\ProductServiceImpl;
use Illuminate\Support\ServiceProvider;
use Validator;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
      // Register validators
      Validator::extend('item_unique_details', IProductService::class . '@validateUniqueItemDetails');
      Validator::extend('item_unique_detail_names', IProductService::class . '@validateUniqueItemDetailNames');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton(IProductService::class, ProductServiceImpl::class);
    }
}
