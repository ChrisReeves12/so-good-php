<?php

namespace App\Console\Commands;

use App\Services\Contracts\IProductService;
use Illuminate\Console\Command;

/**
 * Class UpdateProductStockStatuses
 * @package App\Console\Commands
 */
class UpdateProductStockStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update_stock_statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates all product stock statuses based on available inventory.';

    /** @var IProductService */
    protected $product_service;

  /**
   * Create a new command instance.
   * @param IProductService $product_service
   */
    public function __construct(IProductService $product_service)
    {
        parent::__construct();
        $this->product_service = $product_service;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
      try
      {
        $this->info('Updating products...');
        $this->product_service->updateProductStockStatuses($this);
        $this->info("Updating complete!\n");
        exit(0);
      }
      catch(\Exception $exception)
      {
        $this->error($exception->getMessage());
        exit(1);
      }
    }
}
