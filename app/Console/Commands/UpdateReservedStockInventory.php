<?php

namespace App\Console\Commands;

use App\Services\Contracts\IProductService;
use Illuminate\Console\Command;

/**
 * Class UpdateReservedStockInventory
 * @package App\Console\Commands
 */
class UpdateReservedStockInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update_reserved_inventory';

    /** @var IProductService */
    protected $product_service;

    /**s
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the reserved stock inventory.';

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
     */
    public function handle()
    {
      $this->product_service->updateProductInventory($this);
    }
}
