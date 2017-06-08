<?php

namespace App\Providers;

use App\Services\Contracts\ICartService;
use App\Services\CartServiceImpl;
use Illuminate\Support\ServiceProvider;
use View;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('frontend.layout', function($view) {
          $cartService = $this->app->make(ICartService::class);

          if($cartService instanceof ICartService)
          {
            $view->with('current_cart', $cartService->getCurrentCart());
          }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ICartService::class, CartServiceImpl::class);
    }
}
