<?php
/**
 * The IProductService class definition.
 *
 * The description of the class
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\Item;
use App\Product;
use App\RelStockLocationItem;
use App\StockLocation;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface IProductService
{
  /**
   * Updates all product stock statuses
   * @param Command $command
   */
  public function updateProductStockStatuses($command);


  /**
   * Upload product photo
   * @param int $product_id
   * @param Request $request
   * @return Product
   */
  public function uploadProductPhotos($product_id, $request);

  /**
   * Remove an image from the product
   * @param int $product_id
   * @param string $image_to_remove
   * @param string $new_main_image
   */
  public function deleteProductPhoto($product_id, $image_to_remove, $new_main_image = null);

  /**
   * Update the main image
   * @param int $product_id
   * @param Request $request
   * @return Product
   */
  public function uploadMainImage($product_id, $request);

  /**
   * Upload image for item
   * @param int $item_id
   * @param Request $request
   * @return Item
   */
  public function uploadItemImage($item_id, $request);

  /**
   * Find product by id
   * @param $product_id
   * @return Product
   */
  public function findById($product_id);

  /**
   * Update product index in search
   */
  public function updateProductIndex();

  /**
   * Delete item photo
   * @param $item_id
   */
  public function deleteItemPhoto($item_id);

  /**
   * Updates stock location inventory of items
   * @param Command $command
   */
  public function updateProductInventory($command);

  /**
   * Get listings for product listing page
   * @param string $query_type
   * @param string|int $keyword_or_cat_id
   * @param string $sort_by
   * @param string $price_filter
   * @param int $page
   * @return array
   */
  public function getProductListings(string $query_type, $keyword_or_cat_id, string $sort_by, string $price_filter, int $page): array;

  /**
   * Generate an available sku from a given product id
   * @param int $product_id
   * @return string
   */
  public function generateItemSkuFromProduct(int $product_id): string;

  /**
   * Get product and item data for product page
   * @param Product $product
   * @return array
   */
  public function getProductDataForDetailsPage(Product $product): array;

  /**
   * Find active product by slug
   * @param string $slug
   * @return Product
   */
  public function findActiveProduct(string $slug);

  /**
   * Get recommended products
   * @param Product $product
   * @return array
   */
  public function getRecommendedProducts(Product $product): array;

  /**
   * Find item from options
   * @param int $product_id
   * @param array $option_values
   * @param int $item_id
   * @return Item
   */
  public function findItemFromOptions($product_id, $option_values, $item_id);

  /**
   * Get item data
   * @param Item $item
   * @return array
   */
  public function getItemData(Item $item): array;

  /**
   * Add a product entry into a location
   * @param StockLocation $stock_location
   * @param Item $item
   * @param array $data
   * @return RelStockLocationItem
   */
  public function addInventoryItemEntry(StockLocation $stock_location, Item $item, array $data);

  /**
   * @param $attribute
   * @param $value
   * @return mixed
   */
  public function validateUniqueItemDetailNames($attribute, $value);

  /**
   * @param $attribute
   * @param $value
   * @param $parameters
   * @return mixed
   */
  public function validateUniqueItemDetails($attribute, $value, $parameters);

  /**
   * Find all products
   * @return Collection
   */
  public function findAllProducts();

  /**
   * Find all products that are not inactive
   * @return Collection
   */
  public function findActiveProducts();

  /**
   * Find all items
   * @return Collection
   */
  public function findAllItems();
}