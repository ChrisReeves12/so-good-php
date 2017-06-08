<?php

namespace App\Console\Commands;

use App\Services\Contracts\IProductService;
use App\Services\Contracts\ISalesOrderService;
use Illuminate\Console\Command;

class UpdateSearchIndicies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:update_indicies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update search indicies';

    /** @var ISalesOrderService */
    protected $salesOrderService;

    /** @var IProductService */
    protected $productService;

  /**
   * Create a new command instance.
   * @param IProductService $productService
   * @param ISalesOrderService $salesOrderService
   */
    public function __construct(IProductService $productService, ISalesOrderService $salesOrderService)
    {
        parent::__construct();
        $this->productService = $productService;
        $this->salesOrderService = $salesOrderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $this->info('Update sales order index...');
      $this->salesOrderService->updateSalesOrderIndex();

      $this->info('Updating product index...');
      $this->productService->updateProductIndex();

      $this->info('Done!');
    }
}
