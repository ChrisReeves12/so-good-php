<?php

namespace App\Providers;

use App\Services\Contracts\ILoggerService;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ProductListingController;
use App\Services\AdminLoggerService;
use App\Services\FrontendLoggerService;
use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
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
      // Use correct logger
      if(preg_match('/\/admin/i', $this->app->request->getUri()))
      {
        $this->app->singleton(ILoggerService::class, AdminLoggerService::class);
      }
      else
      {
        $this->app->singleton(ILoggerService::class, FrontendLoggerService::class);
      }
    }
}
