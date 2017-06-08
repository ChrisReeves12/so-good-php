<?php

namespace App\Providers;

use App\Services\Contracts\IProductCategoryService;
use App\Services\ProductCategoryServiceImpl;
use Illuminate\Support\ServiceProvider;

class ProductCategoryServiceProvider extends ServiceProvider
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
      $this->app->singleton(IProductCategoryService::class, ProductCategoryServiceImpl::class);
    }
}
