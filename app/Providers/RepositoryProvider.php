<?php

namespace App\Providers;

use App\Repositories\Contracts\IRepository;
use App\Repositories\ElloquentRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
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
      $this->app->singleton(IRepository::class, ElloquentRepository::class);
    }
}
