<?php
/**
 * The ProductServiceImpl class definition.
 *
 * Product service implementation
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Repositories\Contracts\IRepository;
use App\Services\Contracts\IAdminSearchable;
use App\Services\Contracts\ICRUDRecordTypeService;
use App\Services\Contracts\INoSQLDataSourceService;
use App\Services\Contracts\IProductService;
use App\Services\Contracts\IRecordCopyable;
use App\Item;
use App\NoSQLDataSourceResult;
use App\Product;
use App\RecordType;
use App\RelProductProductCategory;
use App\RelStockLocationItem;
use App\SalesOrder;
use App\StockLocation;
use App\TransactionLineItem;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Validator;
use DB;
use File;
use Illuminate\Support\Collection;

/**
 * Class ProductServiceImpl
 * @package App\Services
 */
class ProductServiceImpl implements IProductService, ICRUDRecordTypeService, IAdminSearchable, IRecordCopyable
{
  /** @var IRepository */
  protected $sqlRepository;

  /** @var INoSQLDataSourceService */
  protected $noSQLDataSource;

  /**
   * ProductServiceImpl constructor.
   * @param IRepository $repository
   * @param INoSQLDataSourceService $noSQLDataSource
   */
  public function __construct(IRepository $repository, INoSQLDataSourceService $noSQLDataSource)
  {
    $this->sqlRepository = $repository;
    $this->noSQLDataSource = $noSQLDataSource;
  }

  /**
   * Updates all product stock statuses
   * @param Command $command
   */
  public function updateProductStockStatuses($command)
  {
    // Load all products
    $products = $this->sqlRepository->use(Product::class)->findAll();
    $command->info($products->count() . ' products loaded...');
    $num_of_products = $products->count();
    $num_of_items = 0;
    $in_stock_items = 0;
    $out_of_stock_items = 0;

    /** @var Product $product */
    foreach($products as $product)
    {
      $this->sqlRepository->beginTransaction();

      $command->line('Evaluating product ID: ' . $product->id);
      $items = $product->items;

      if($items->isNotEmpty())
      {
        /** @var Item $item */
        foreach($items as $item)
        {
          $command->line('Evaluating item ID: ' . $item->id);
          $item->calculateStockStatus();
          $command->comment('Item ID: ' . $item->id . ' is ' . $item->calculated_stock_status);

          if($item->calculated_stock_status == 'in_stock')
          {
            $in_stock_items++;
          }
          elseif($item->calculated_stock_status == 'out_of_stock')
          {
            $out_of_stock_items++;
          }

          $item->save();
          $num_of_items++;
        }
      }
      else
      {
        $command->line('Evaluating item ID: ' . $product->default_item_id);
        $product->default_item->calculateStockStatus();
        $command->comment('Item ID: ' . $product->default_item_id . ' is ' . $product->default_item->calculated_stock_status);
        $product->default_item->save();

        if($product->default_item->calculated_stock_status == 'in_stock')
        {
          $in_stock_items++;
        }
        elseif($product->default_item->calculated_stock_status == 'out_of_stock')
        {
          $out_of_stock_items++;
        }

        $num_of_items++;
      }

      $this->sqlRepository->commitTransaction();
    }

    $command->info('Number of Products Evaluated: ' . $num_of_products . ' | Number of Items Evaluated: ' . $num_of_items);
    $command->info('Number of In Stock Items: ' . $in_stock_items . ' | Number of Out Of Stock Items: ' . $out_of_stock_items);

    $command->info('Done!');
  }

  /**
   * Delete item photo
   * @param $item_id
   */
  public function deleteItemPhoto($item_id)
  {
    $item = $this->sqlRepository->use(Item::class)->find($item_id);
    $file_name = $item->image;
    $item->image = null;
    $this->sqlRepository->save($item);

    // Remove image
    $item->deleteImageFromDisk($file_name);
  }

  /**
   * Remove an image from the product
   * @param int $product_id
   * @param string $image_to_remove
   * @param null $new_main_image
   * @throws \Exception
   */
  public function deleteProductPhoto($product_id, $image_to_remove, $new_main_image = null)
  {
    $product = $this->sqlRepository->use(Product::class)->find($product_id);
    if(!($product instanceof Product))
    {
      throw new \Exception('The product with id: ' . $product_id . ' could not be found.');
    }

    $images = new Collection($product->images);
    if(empty($images))
    {
      throw new \Exception('The image being deleted does not exist.');
    }

    // Filter out the image to remove from images array
    $images = $images->filter(function($img) use ($image_to_remove)
    {
      return ($img != $image_to_remove);
    });

    // Update main image if needed
    if(!empty($new_main_image))
    {
      $product->default_image = $new_main_image;
    }

    // If all images are deleted, clear the main image
    if($images->filter(function($img) use ($image_to_remove) { return ($img != $image_to_remove); })->isEmpty())
    {
      $product->default_image = null;
    }

    $product->images = $images->toArray();
    $this->sqlRepository->save($product);

    $product->deleteImageFromDisk($image_to_remove);
  }

  /**
   * Upload product photo
   * @param int $product_id
   * @param Request $request
   * @return Product
   * @throws \Exception
   */
  public function uploadProductPhotos($product_id, $request)
  {
    $product = $this->sqlRepository->use(Product::class)->find($product_id);
    $allowed_mime_types = ['image/png', 'image/gif', 'image/jpeg'];

    if(!($product instanceof Product))
    {
      throw new \Exception('Could not find product with id: ' . $product_id);
    }

    if($request->hasFile('file'))
    {
      // Check file extension
      if(in_array($request->file('file')->getMimeType(), $allowed_mime_types))
      {
        // Move file
        $file_name = strtolower(preg_replace('/\s+/', '_', $request->file('file')->getClientOriginalName()));
        $path = $request->file('file')->storeAs($product_id, $file_name, 'product_images');

        if($path)
        {
          // Save file to database, only add image if it isn't already added
          if(empty($product->images) || !in_array($file_name, $product->images))
          {
            $images = $product->images ?? [];
            $images[] = $file_name;

            // Set default image if there isn't one set or it isn't in the list of images
            if(empty($product->default_image) ||
              empty(array_filter($images, function($image) use ($product)
              {
                return ($product->default_image == $image);
              }))
            )
            {
              if(!empty($images))
              {
                $product->setAttribute('default_image', $images[0]);
              }
            }

            $product->images = $images;
            $this->sqlRepository->save($product);
          }
        }
        else
        {
          throw new \Exception('An error occurred while moving the uploaded file to the correct directory on the server.');
        }
      }
      else
      {
        throw new \Exception('The system only supports uploading PNG, GIF or JPG files.');
      }
    }
    else
    {
      throw new \Exception('No file uploaded.');
    }

    return $product;
  }

  /**
   * Updates stock location inventory of items
   * @param Command $command
   */
  public function updateProductInventory($command)
  {
    $command->info('Updating inventory...');
    $items = $this->sqlRepository->use(Item::class)->findAll();

    /** @var Item $item */
    foreach($items as $item)
    {
      $command->line('Evaluating Item ID: ' . $item->id);
      $rel_stock_locations = $item->rel_stock_location_items()->where('is_inactive', false)->get();

      /** @var RelStockLocationItem $rel_stock_location */
      foreach($rel_stock_locations as $rel_stock_location)
      {
        $quantity_reserved = 0;
        $total_quantity = ($rel_stock_location->quantity_available ?? 0);
        $total_quantity += ($rel_stock_location->quantity_reserved ?? 0);

        // Open all order lines on this line item
        $line_items = TransactionLineItem::whereRaw('item_id = ? AND transaction_line_itemable_type =? AND status != ?',
          [$item->id, 'SalesOrder', 'out_of_stock'])
          ->get();

        // Filter out line items that are not shipping from this location
        $line_items = $line_items->filter(function(TransactionLineItem $tli) use($rel_stock_location) {
          return($tli->ship_from_location_id == $rel_stock_location->stock_location_id);
        })
          ->filter(function(TransactionLineItem $tli) {
            // Filter out lines that are on canceled or shipped orders
            $parent_sales_order = SalesOrder::where('transaction_id', $tli->transaction_id)->first();
            return(in_array($parent_sales_order->status, ['pending', 'processing']));
          });

        /** @var TransactionLineItem $line_item */
        foreach($line_items as $line_item)
        {
          $quantity_reserved += $line_item->quantity;
        }

        $quantity_available = $total_quantity - $quantity_reserved;

        if($quantity_available < 0)
          $quantity_available = 0;

        if($quantity_reserved < 0)
          $quantity_reserved = 0;

        $rel_stock_location->quantity_reserved = $quantity_reserved;
        $rel_stock_location->quantity_available = $quantity_available;
        $rel_stock_location->save();

        $command->comment('Stock Location: ' . $rel_stock_location->stock_location->name . ' | Reserved: ' . $quantity_reserved . ' | Avail: ' . $quantity_available);
      }
    }

    $command->info('Complete!');
  }

  /**
   * Gets listings for product listing page
   * @param string $query_type
   * @param string|int $keyword_or_cat_id
   * @param string $sort_by
   * @param string $price_filter
   * @param int $page
   * @return array
   */
  public function getProductListings(string $query_type, $keyword_or_cat_id, string $sort_by, string $price_filter, int $page = 1): array
  {
    $page = $page - 1;
    $options = ['start' => ($page * business('products_per_page')), 'facets' => ['brand'], 'max_results' => business('products_per_page')];
    $ret_val = ['brand_facets' => new Collection(), 'listings' => new Collection()];
    $query_params = [];

    // Handle price filter
    if($price_filter != 'all')
    {
      list($price_low, $price_high) = explode('_', $price_filter);
      $query_params['store_price'] = ['between' => [$price_low, $price_high]];
    }

    // Handle sort
    switch($sort_by)
    {
      case 'newest':
        $options['sort_by'] = ['timestamp', 'desc'];
        break;

      case 'price_asc':
        $options['sort_by'] = ['store_price', 'asc'];
        break;

      case 'price_desc':
        $options['sort_by'] = ['store_price', 'desc'];
        break;
    }

    if($query_type == 'search')
    {
      $keyword = addslashes($keyword_or_cat_id);
      $query_params = array_merge($query_params, ['*all*' => $keyword, 'in_stock' => 'true']);
    }
    else // Category search
    {
      $cat_id = $keyword_or_cat_id;
      $query_params = array_merge($query_params, ['category_ids' => $cat_id, 'in_stock' => 'true']);
    }

    $result_set = $this->noSQLDataSource->findBy('products', $query_params, $options);
    $ret_val['listings'] = $result_set->getResults();
    $ret_val['brand_facets'] = $result_set->getFacets();
    $ret_val['num_of_listings'] = $result_set->getTotalResultsCount();

    return $ret_val;
  }

  /**
   * Save or update product
   * @param Product $product
   * @param array $data
   * @return array
   */
  public function createUpdate($product, array $data = [])
  {
    $ret_val = ['errors' => []];

    // Validate
    $is_valid = true;
    if (!empty($product->getValidationRules()))
    {
      $validator = Validator::make($data, $product->getValidationRules(), $product->getValidationMessages());
      if ($validator->fails())
      {
        $is_valid = false;
        $error_keys = array_keys($validator->errors()->toArray());
        foreach ($error_keys as $error_key)
        {
          $ret_val['errors'][] = [$error_key => $validator->errors()->get($error_key)];
        }
      }
    }

    if ($is_valid)
    {
      $product_data = [
        'name'              => $data['name'],
        'description'       => $data['description'],
        'tags'              => $data['tags'],
        'model_number'      => $data['model_number'],
        'is_inactive'       => $data['is_inactive'],
        'affiliate_allowed' => $data['affiliate_allowed'],
        'slug'              => $data['slug'],
        'vendor_id'         => $data['vendor_id'],
        'specs'             => $data['specs']
      ];

      // Set basic attributes
      if ($product->exists)
      {
        $product->update($product_data);
      }
      else
      {
        $product = Product::create($product_data);
      }

      $stock_status_override = null;
      if(!empty($data['stock_status_override']) && in_array($data['stock_status_override'], ['in_stock', 'out_of_stock']))
      {
        $stock_status_override = $data['stock_status_override'];
      }

      // Set item attributes
      $default_item_data = [
        'sku'                    => $data['sku'],
        'list_price'             => $data['list_price'],
        'store_price'            => $data['store_price'],
        'is_inactive'            => $data['is_inactive'],
        'product_id'             => $product->id,
        'weight'                 => $data['weight'],
        'ships_alone'            => $data['ships_alone'],
        'upc'                    => $data['upc'],
        'stock_status_override'  => $stock_status_override,
        'isbn'                   => $data['isbn'],
        'ean'                    => $data['ean'],
        'cost'                   => $data['cost'],
        'is_default'             => true
      ];


      if(empty($product->default_item))
      {
        $product->default_item()->associate(Item::create($default_item_data));
      }
      else
      {
        $product->default_item->update($default_item_data);
      }

      // Update stock location data for single sku product
      if ($product->non_default_items->isEmpty())
      {
        if(!empty($data['default_item_stock_data']) && is_array($data['default_item_stock_data']))
        {
          foreach($data['default_item_stock_data'] as $stock_location_data)
          {
            $rel_stock_location = RelStockLocationItem::where('stock_location_id', $stock_location_data['id'])
              ->where('item_id', $product->default_item_id)->first();

            if(empty($rel_stock_location))
            {
              RelStockLocationItem::create([
                'stock_location_id' => $stock_location_data['id'],
                'item_id' => $product->default_item_id,
                'quantity_available' => $stock_location_data['qty'],
                'is_inactive' => ($stock_location_data['active'] != 'true')
              ]);
            }
            else
            {
              $rel_stock_location->update([
                'stock_location_id' => $stock_location_data['id'],
                'item_id' => $product->default_item_id,
                'quantity_available' => $stock_location_data['qty'],
                'is_inactive' => ($stock_location_data['active'] != 'true')
              ]);
            }
          }
        }
      }

      // Create new category associations
      $category_ids = empty($data['categories']) ? [] : array_map(function ($cat)
      {
        return $cat['id'];
      }, $data['categories']);

      if (!empty($category_ids))
      {
        $product->rel_product_product_categories()->getQuery()->delete();
        foreach ($category_ids as $category_id)
        {
          RelProductProductCategory::create(['product_id' => $product->id, 'product_category_id' => $category_id]);
        }
      }

      $product->save();

      $ret_val = ['errors' => false, 'id' => $product->id];
    }

    return $ret_val;
  }

  /**
   * Copies a product record
   * @param Product $record
   * @param RecordType $record_type
   * @param bool $record_has_inactive
   * @return array
   */
  public function copyRecord($record, RecordType $record_type, $record_has_inactive = false): array
  {
    $ret_val = ['system_error' => false, 'edit_url' => null];

    // Get base table copy
    /** @var Product $copied_record */
    $copied_record = $record->replicate(['slug', 'default_item_id', 'cached_options']);
    $copied_record->slug = $record->slug . '-' . time();

    if($record_has_inactive)
    {
      $copied_record->is_inactive = true;
    }

    $copied_record->save();
    $sku_counter = time();

    // Copy default item
    /** @var Item $default_item_copy */
    $default_item_copy = $record->default_item->replicate(['sku', 'upc']);
    $default_item_copy->sku = 'copy-' . $sku_counter;
    $default_item_copy->product()->associate($copied_record);
    $default_item_copy->save();
    $copied_record->default_item()->associate($default_item_copy);
    $copied_record->save();

    // Copy item stock locations
    /** @var RelStockLocationItem $stock_location_item */
    foreach($default_item_copy->rel_stock_location_items as $stock_location_item)
    {
      /** @var RelStockLocationItem $default_stock_loc_item_copy */
      $default_stock_loc_item_copy = $stock_location_item->replicate(['item_id']);
      $default_stock_loc_item_copy->item()->associate($default_item_copy);
      $default_stock_loc_item_copy->save();
    }

    // Copy categories
    /** @var RelProductProductCategory $rel_product_category */
    foreach($record->categories as $rel_product_category)
    {
      /** @var RelProductProductCategory $new_rel_product_category */
      $new_rel_product_category = $rel_product_category->replicate(['product_id']);
      $new_rel_product_category->product()->associate($copied_record);
      $new_rel_product_category->save();
    }

    // Copy items
    /** @var Item $non_default_item */
    $idx = 1;
    foreach($record->non_default_items as $non_default_item)
    {
      /** @var Item $non_default_item_copy */
      $non_default_item_copy = $non_default_item->replicate(['product_id', 'upc', 'sku']);
      $non_default_item_copy->sku = 'copy-' . ($sku_counter + $idx);
      $non_default_item_copy->product()->associate($copied_record);
      $non_default_item_copy->save();

      File::copyDirectory(business('site_root') . '/storage/app/public/content-img/products/items/' . $non_default_item->id,
        business('site_root') . '/storage/app/public/content-img/products/items/' . $non_default_item_copy->id);

      // Update stock locations
      foreach($non_default_item->rel_stock_location_items as $stock_location_item)
      {
        /** @var RelStockLocationItem $non_default_stock_loc_item */
        $non_default_stock_loc_item = $stock_location_item->replicate(['item_id']);
        $non_default_stock_loc_item->item()->associate($non_default_item_copy);
        $non_default_stock_loc_item->save();
      }

      $idx++;
    }

    // Move images
    File::copyDirectory(business('site_root') . '/storage/app/public/content-img/products/' . $record->id,
      business('site_root') . '/storage/app/public/content-img/products/' . $copied_record->id);

    $ret_val['edit_url'] = $record_type->edit_url . '/' . $copied_record->id;

    return $ret_val;
  }

  /**
   * Returns results for admin search
   * @param string $keyword
   * @return array
   */
  public function handleAdminSearch(string $keyword): array
  {
    $ret_val = [];
    $results = $this->noSQLDataSource->findBy('products', ['*all*' => $keyword], ['max_results' => 10]);

    if($results->getResults()->count() > 0)
    {
      /** @var NoSQLDataSourceResult $result */
      foreach($results->getResults() as $result)
      {
        $ret_val[] = [
          'image' => $result->get('image'),
          'id' => $result->get('id'),
          'name' => $result->get('name'),
          'link' => '/admin/product/' . $result->get('id')
        ];
      }
    }

    return $ret_val;
  }

  /**
   * Get product and item data for product page
   * @param Product $product
   * @return array
   */
  public function getProductDataForDetailsPage(Product $product): array
  {
    $page_title = $product->name;
    $product_options = [];

    // Get options for product
    /** @var Item $item */
    if($product->non_default_items->isNotEmpty())
    {
      foreach($product->non_default_items as $item)
      {
        $details = $item->details;
        foreach($details as $detail)
        {
          $option_name = trim($detail['key']);

          if(!isset($product_options[$option_name]))
          {
            $product_options[$option_name] = [];
          }

          if(!in_array($detail['value'], $product_options[$option_name]))
          {
            $product_options[$option_name][] = $detail['value'];
          }
        }
      }
    }

    // Sort product options
    if(!empty($product_options))
    {
      $product_options = array_map(function($e) {
        sort($e);

        return $e;
      }, $product_options);
    }

    // Get stock status and quantity if it is a single sku product
    if(empty($product_options))
    {
      $stock_status = ($product->default_item->stock_status_override != 'none' && !empty($product->default_item->stock_status_override)) ?
        $product->default_item->stock_status_override : $product->default_item->calculated_stock_status;

      $orderable_quantity = $product->default_item->single_orderable_quantity;
    }

    // Get product data
    $default_sku = $product->default_item->sku;
    $product_data = [
      'name'              => $product->name,
      'id'                => $product->id,
      'images'            => $product->view_images,
      'description'       => $product->description,
      'specs'             => $product->specs,
      'default_image_url' => $product->default_image_url,
      'quantity'          => $orderable_quantity ?? null,
      'stock_status'      => $stock_status ?? null
    ];

    // Create price displays
    $price_display = '$' . number_format($product->default_item->store_price, '2');
    $list_price_display = !empty($product->default_item->list_price) ? '$' . number_format($product->default_item->list_price, '2') : null;

    if($product->non_default_items->isNotEmpty())
    {
      // Get regular price display among items
      $item_prices = [];
      $item_list_prices = [];

      foreach($product->non_default_items as $non_default_item)
      {
        $price = floatval(trim($non_default_item->store_price));
        $price = number_format($price, 2);

        if(!empty($non_default_item->list_price))
        {
          $list_price = floatval(trim($non_default_item->list_price));
          $list_price = number_format($list_price, 2);

          if(!in_array($list_price, $item_list_prices))
          {
            $item_list_prices[] = $list_price;
          }
        }

        if(!in_array($price, $item_prices))
        {
          $item_prices[] = $price;
        }

      }

      sort($item_prices);

      if(!empty($item_list_prices))
      {
        sort($item_list_prices);
        $item_list_prices = new Collection($item_list_prices);
        if($item_list_prices->count() > 1)
        {
          $list_price_display = "\${$item_list_prices->last()}";
        }
        else
        {
          $list_price_display = "\${$item_list_prices->first()}";
        }
      }

      $item_prices = new Collection($item_prices);
      if($item_prices->count() > 1 && $item_prices->first() < $item_prices->last())
      {
        $price_display = "\${$item_prices->first()} - \${$item_prices->last()}";
      }
      else
      {
        $price_display = "\${$item_prices->first()}";
      }
    }

    $brand_name = $product->vendor->name;

    return [
      'brand_name' => $brand_name,
      'price_display' => $price_display,
      'default_sku' => $default_sku,
      'list_price_display' => $list_price_display,
      'product_options' => $product_options,
      'product' => $product_data
    ];
  }

  /**
   * Find active product by slug
   * @param string $slug
   * @return Product
   */
  public function findActiveProduct(string $slug)
  {
    return $this->sqlRepository
      ->use(Product::class)
      ->findOneBySql('lower(slug) = lower(?) AND is_inactive != ?', [$slug, true]);
  }

  /**
   * Generate an item sku from a given product id
   * @param int $product_id
   * @return string
   */
  public function generateItemSkuFromProduct(int $product_id): string
  {
    $new_sku = '';
    $product = Product::find($product_id);
    $sku = $product->sku;
    $idx = 1;
    $found = false;
    while(!$found)
    {
      $new_sku = $sku . '-' . $idx;
      if(Item::where('sku', $new_sku)->count() == 0)
        $found = true;

      $idx++;
    }

    return $new_sku;
  }

  /**
   * Get recommended products
   * @param Product $product
   * @return array
   */
  public function getRecommendedProducts(Product $product): array
  {
    $recommended_products = [];

    if(!empty($product->tags))
    {
      $tags = addslashes($product->tags);
      $result_set = $this->noSQLDataSource->findBy('products', ['is_inactive' => 'false', 'in_stock' => 'true', '*all*' => $tags], ['max_results' => 7]);

      if($result_set->getResults()->isNotEmpty())
      {
        /** @var NoSQLDataSourceResult $result */
        foreach($result_set->getResults() as $result)
        {
          if($result->get('id') == $product->id)
          {
            continue;
          }

          $recommended_products[] = $result->getData();
        }
      }
    }

    return $recommended_products;
  }

  /**
   * Find item from options
   * @param int $product_id
   * @param array $option_values
   * @param int $item_id
   * @return Item
   */
  public function findItemFromOptions($product_id, $option_values, $item_id)
  {
    if(!empty($option_values))
    {
      $item = Item::findFromOptions(intval($product_id), $option_values);
    }
    else
    {
      $item = Item::whereRaw('id = ? AND is_inactive = ?', [$item_id, false])->first();
    }

    return $item;
  }

  /**
   * Get item data
   * @param Item $item
   * @return array
   */
  public function getItemData(Item $item): array
  {
    $ret_val['item'] = $item->toArray();
    $ret_val['item']['image'] = $item->view_image;
    $ret_val['item']['quantity'] = $item->single_orderable_quantity;
    $ret_val['item']['stock_status'] = ($item->stock_status_override != 'none') ?
      $item->stock_status_override : $item->calculated_stock_status;

    $ret_val['item']['list_item'] = $item->list_price ?? 0;

    return $ret_val;
  }

  /**
   * Validator to check if each of the detail names are different
   * @param $attribute
   * @param $value
   * @return bool
   */
  public function validateUniqueItemDetailNames($attribute, $value)
  {
    $details = new Collection($value);
    $detail_names = $details->map(function($detail) { return strtolower(trim($detail['key'])); });

    return $detail_names->unique()->count() == $details->count();
  }

  /**
   * Validator to check if details are unique
   * @param string $attribute
   * @param array $value
   * @param array $parameters
   * @return bool
   */
  public function validateUniqueItemDetails($attribute, $value, $parameters)
  {
    // Find product this item belongs to
    try
    {
      /** @var Product $product */
      $product = Product::find($parameters[0]);

      // Create detail signature for given details
      $current_details = new Collection($value);
      $current_detail_signature = $current_details->flatten()
        ->map(function($d) { return(trim(strtolower($d))); })
        ->sort()
        ->toJson();

      // Get detail signatures of sibling items
      $other_item_detail_signatures = $product->non_default_items
        ->filter(function(Item $itm) use($parameters) {
          return(empty($parameters[1]) || $parameters[1] != $itm->id);
        })
        ->map(function(Item $itm) {

          $details = new Collection($itm->details);

          return $details->flatten()
            ->map(function($d) { return(trim(strtolower($d))); })
            ->sort()
            ->toJson();
        });

      // If the current signature exists in the others, fail validation
      $is_valid = !$other_item_detail_signatures->contains($current_detail_signature);
    }
    catch(\Exception $ex)
    {
      $is_valid = false;
    }

    return $is_valid;
  }

  /**
   * Find all products
   * @return Collection
   */
  public function findAllProducts()
  {
    return Product::all();
  }

  /**
   * Find all items
   * @return Collection
   */
  public function findAllItems()
  {
    return Item::all();
  }

  /**
   * Find all products that are not inactive
   * @return Collection
   */
  public function findActiveProducts()
  {
    return Product::where("is_inactive", false)->get();
  }

  /**
   * Add a product entry into a location
   * @param StockLocation $stock_location
   * @param Item $item
   * @param array $data
   * @return RelStockLocationItem
   */
  public function addInventoryItemEntry(StockLocation $stock_location, Item $item, array $data)
  {
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    return RelStockLocationItem::create([
      'stock_location_id' => $stock_location->id,
      'item_id' => $item->id,
      'is_inactive' => $data['is_inactive'],
      'quantity_available' => $data['quantity_available'],
      'quantity_reserved' => $data['quantity_reserved']
    ]);
  }

  /**
   * Find product by id
   * @param $product_id
   * @return Product
   */
  public function findById($product_id)
  {
    return Product::find($product_id);
  }

  /**
   * Update the main image
   * @param int $product_id
   * @param Request $request
   * @return Product
   */
  public function uploadMainImage($product_id, $request)
  {
    $product = $this->findById($product_id);
    $product->default_image = $request->get('main_image')['file_name'];
    $product->save();

    return $product;
  }

  /**
   * Upload item image
   * @param int $item_id
   * @param Request $request
   * @return Item
   * @throws \Exception
   */
  public function uploadItemImage($item_id, $request)
  {
    $allowed_mime_types = ['image/png', 'image/gif', 'image/jpeg'];
    $item = Item::find($item_id);

    if(!($item instanceof Item))
    {
      throw new \Exception('Item id: ' . $item->id . ' could not be found.');
    }

    // Check file extension
    if(in_array($request->file('file')->getMimeType(), $allowed_mime_types))
    {
      $file_name = strtolower(preg_replace('/\s+/', '_', $request->file('file')->getClientOriginalName()));
      $path = $request->file('file')->storeAs($item->id, $file_name, 'item_images');

      if($path)
      {
        $item->image = $file_name;
        $item->save();
      }
    }

    return $item;
  }

  /**
   * Update product index in search
   */
  public function updateProductIndex()
  {
    $result = $this->noSQLDataSource->updateCollectionIndex('products', Product::class);
    if(!$result['success'])
    {
      throw $result['error'];
    }
  }
}