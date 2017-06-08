<?php

namespace App\Console\Commands;

use App\Services\Contracts\IProductService;
use App\Product;
use Illuminate\Console\Command;

/**
 * Class ProductPageRequestCheck
 * @package App\Console\Commands
 */
class ProductPageRequestCheck extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'product_page:request_check';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sends a request to all product pages to see if they load.';

  /**
   * @var IProductService
   */
  protected $productService;

  /**
   * ProductPageRequestCheck constructor.
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
    $bad_product_report_file = '/tmp/bad_product_report.csv';
    $fp = fopen($bad_product_report_file, 'w');
    fwrite($fp, "id,slug,product_name,date_evaluated\n");
    $count = 0;
    $bad_product_count = 0;

    $this->info("Checking if each product page loads...");
    $products = $this->productService->findActiveProducts();
    if($products->isNotEmpty())
    {
      /** @var Product $product */
      foreach($products as $product)
      {
        $this->line('Evaluating Product ID: ' . $product->id);

        $ch = curl_init(business('site_url') .'/' . $product->slug);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $results = curl_exec($ch);

        // Didn't load correctly, log it
        if(empty($results) || !preg_match('/200 OK/i', $results))
        {
          $string = $product->id . ',' . $product->slug . ',' . $product->name . ',' . (new \DateTime('now', new \DateTimeZone(business('timezone'))))->format('m/d/Y') . "\n";
          fwrite($fp, $string);
          $bad_product_count++;
        }

        $count++;
      }
    }

    fclose($fp);
    $this->info('Complete! ' . $count . ' products evaluated and found ' . $bad_product_count . ' product(s) that need review.');
  }
}
