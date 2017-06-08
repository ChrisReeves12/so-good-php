<?php

namespace App\Console;

use App\Console\Commands\CollectShoppingCartData;
use App\Console\Commands\ConvertDescLinksToHttps;
use App\Console\Commands\GetDataFromOldDB;
use App\Console\Commands\InventoryDataFix;
use App\Console\Commands\MakeService;
use App\Console\Commands\ProductPageRequestCheck;
use App\Console\Commands\SyncNewsletterSignups;
use App\Console\Commands\SyncTransactionEmailsToNewsletterSignups;
use App\Console\Commands\UpdateProductStockStatuses;
use App\Console\Commands\UpdateReservedStockInventory;
use App\Console\Commands\UpdateSearchIndicies;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      UpdateProductStockStatuses::class,
      UpdateReservedStockInventory::class,
      InventoryDataFix::class,
      ProductPageRequestCheck::class,
      ConvertDescLinksToHttps::class,
      SyncNewsletterSignups::class,
      MakeService::class,
      CollectShoppingCartData::class,
      SyncTransactionEmailsToNewsletterSignups::class,
      UpdateSearchIndicies::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      $schedule->command('product:update_reserved_inventory')->everyMinute()->sendOutputTo('/tmp/update_reserved_inventory.log');
      $schedule->command('product:update_stock_statuses')->everyMinute()->sendOutputTo('/tmp/update_stock_status.log');
      $schedule->command('product:fix_http_desc')->everyMinute();
      $schedule->command('cart:update_open_carts')->daily()->sendOutputTo('/tmp/update_open_carts.log');
      $schedule->command('mail:sync_newsletter_signups')->everyFiveMinutes()->sendOutputTo('/tmp/newsletter_signups.log');
      $schedule->command('product:add_missing_stock_locations')->hourly()->sendOutputTo('/tmp/add_missing_stock_locations.log');
      $schedule->command('search:update_indicies')->everyFiveMinutes()->sendOutputTo('/tmp/update_indicies.log');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
