<?php

namespace App\Providers;

use App\Services\Contracts\IArticleService;
use App\Services\ArticleServiceImpl;
use Illuminate\Support\ServiceProvider;

class ArticleServiceProvider extends ServiceProvider
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
      $this->app->singleton(IArticleService::class, ArticleServiceImpl::class);
    }
}
