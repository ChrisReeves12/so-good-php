<?php

namespace App\Providers;

use App\Services\Contracts\IAuthService;
use App\Services\AuthServiceImpl;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function register()
    {
      $this->app->singleton(IAuthService::class, AuthServiceImpl::class);
    }
}
