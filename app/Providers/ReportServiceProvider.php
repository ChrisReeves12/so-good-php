<?php

namespace App\Providers;

use App\Services\Contracts\IReportService;
use App\Services\ReportServiceImpl;
use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
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
        $this->app->singleton(IReportService::class, ReportServiceImpl::class);
    }
}
