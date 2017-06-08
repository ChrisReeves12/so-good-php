<?php

namespace App\Console\Commands;

use App\Services\Contracts\IProductService;
use App\Services\Contracts\IStockLocationService;
use App\Item;
use Illuminate\Console\Command;

/**
 * Class InventoryDataFix
 * @package App\Console\Commands
 */
class InventoryDataFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:add_missing_stock_locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds all missing stock locations to new products.';

  /**
   * @var IStockLocationService
   */
    protected $stockLocationService;

  /**
   * @var IProductService
   */
    protected $productService;

  /**
   * Create a new command instance.
   * @param IStockLocationService $stockLocationService
   * @param IProductService $productService
   */
    public function __construct(IStockLocationService $stockLocationService, IProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
        $this->stockLocationService = $stockLocationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $count = 0;
      $this->info('Adding missing stock location items...');
      $stock_locations = $this->stockLocationService->findAll();
      $items = $this->productService->findAllItems();


      /** @var Item $item */
      foreach($items as $item)
      {
        $this->line('Evaluating item ID: ' . $item->id);

        // Check if this item has all of the stock locations
        $missing_locations = [];
        foreach($stock_locations as $stock_location)
        {
          if($item->rel_stock_location_items()->where('stock_location_id', $stock_location->id)->count() == 0)
            $missing_locations[] = $stock_location;
        }

        if(!empty($missing_locations))
        {
          $this->comment('Repairing item ID: ' . $item->id);
          foreach($missing_locations as $missing_location)
          {
            $this->productService->addInventoryItemEntry($missing_location,
              $item, ['is_inactive' => true, 'quantity_available' => 0, 'quantity_reserved' => 0]);
          }

          $count++;
        }
      }

      $this->info('Complete! ' . $count . ' items repaired...');
    }
}
