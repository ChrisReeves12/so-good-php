<?php

namespace App\Console\Commands;

use App\Services\Contracts\IProductService;
use App\Product;
use Illuminate\Console\Command;

/**
 * Class ConvertDescLinksToHttps
 * @package App\Console\Commands
 */
class ConvertDescLinksToHttps extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'product:fix_http_desc';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Replaces references of http to https in product descriptions.';

  /** @var IProductService */
  protected $productService;

  /**
   * Create a new command instance.
   * @param IProductService $productService
   */
  public function __construct(IProductService $productService)
  {
    parent::__construct();
    $this->productService = $productService;
  }

  /**
   * Execute the console command.
   */
  public function handle()
  {
    /** @var Product $product */
    foreach($this->productService->findAllProducts() as $product)
    {
      $new_desc = preg_replace('/http\:\/\//i', 'https://', $product->description);
      $product->update(['description' => $new_desc]);
    }
  }
}
