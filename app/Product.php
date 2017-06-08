<?php

namespace App;

use App\Contracts\ISolrDocumentable;
use Illuminate\Database\Eloquent\Collection;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;

class Product extends AbstractRecordType implements ISolrDocumentable
{
  protected $guarded = ['admin'];
  protected $casts = ['images' => 'array', 'specs' => 'array'];
  protected $non_default_items_list;
  protected $sales_count;

  public function getViewParameters(): array
  {
    return ['id', 'name', 'stock_location_items', 'description', 'cost', 'tags', 'list_price', 'store_price',
      'model_number', 'upc', 'view_categories', 'isbn', 'ean', 'slug', 'sku', 'view_images', 'default_image', 'weight',
      'vendor_id', 'main_stock_location_id', 'is_inactive', 'ships_alone', 'affiliate_allowed', 'calculated_stock_status',
      'default_stock_location_items', 'quantity', 'can_preorder', 'specs', 'non_default_items', 'stock_status_override'];
  }

  public function canBeDuplicated()
  {
    return true;
  }

  public function getTotalSales()
  {
    if(!isset($this->sales_count))
    {
      $sales_count = 0;

      /** @var Item $item */
      foreach($this->items as $item)
      {
        $sales_count += $item->getTotalSales();
      }

      $this->sales_count = $sales_count;
    }

    return $this->sales_count;
  }

  public function getTotalSalesAttribute()
  {
    return $this->getTotalSales();
  }

  public function getDefaultImageDisplayAttribute()
  {
    $ret_val = '';
    if(!empty($this->default_image))
    {
      $ret_val = "<img src='" . $this->getImageUrl($this->default_image) . "'/>";
    }

    return $ret_val;
  }

  public function getStockStatusOverrideAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->stock_status_override : null;
  }

  public function getCalculatedStockStatusAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->calculated_stock_status : null;
  }

  /** Item[] */
  public function items()
  {
    return $this->hasMany('App\Item');
  }

  public function categories()
  {
    return $this->hasMany('App\RelProductProductCategory');
  }

  public function vendor()
  {
    return $this->belongsTo('App\Vendor');
  }

  public function getStorePriceAttribute()
  {
    return !empty($this->default_item) ? money_format('%i', $this->default_item->store_price) : 0.00;
  }

  public function getCostAttribute()
  {
    return !empty($this->default_item) ? money_format('%i', $this->default_item->cost) : 0.00;
  }

  public function getListPriceAttribute()
  {
    return !empty($this->default_item) ? money_format('%i', $this->default_item->list_price) : 0.00;
  }

  public function getUpcAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->upc : null;
  }

  public function getIsbnAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->isbn : null;
  }

  public function getImageUrl($file_name)
  {
    return ('/content-img/products/' . $this->id . '/' . $file_name);
  }

  public function getViewImagesAttribute()
  {
    $ret_val = [];

    if(!empty($this->attributes['images']))
    {
      $images = json_decode($this->attributes['images']);
      if (!empty($images))
      {
        $images = new Collection($images);
        $ret_val = $images->map(function ($img)
        {
          return [
            'file_name' => $img,
            'url'       => $this->getImageUrl($img),
            'is_main'   => (!empty($this->default_image) && $img == $this->default_image)
          ];
        });
      }
    }

    return $ret_val;
  }

  public function getEanAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->ean : null;
  }

  public function getWeightAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->weight : 0.00;
  }

  public function default_item()
  {
    return $this->belongsTo('App\Item', 'default_item_id', 'id');
  }

  public function getViewCategoriesAttribute()
  {
    return $this->categories->map(function (RelProductProductCategory $rppc)
    {
      return [
        'id'    => $rppc->product_category_id,
        'label' => $rppc->product_category->name
      ];
    });
  }

  public function getQuantityAttribute()
  {
    $quantity = 0;

    // Get total quantity of all items if there are multiple items
    if (!$this->getNonDefaultItemsAttribute()->isEmpty())
    {
      /** @var Item $item */
      foreach ($this->getNonDefaultItemsAttribute() as $item)
      {
        $quantity += $item->getTotalQuantityAttribute();
      }
    }
    elseif (!empty($this->default_item))
    {
      // Get total of default item
      $quantity = $this->default_item->getTotalQuantityAttribute();
    }

    return $quantity;
  }

  public function getShipsAloneAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->ships_alone : false;
  }

  public function getDefaultStockLocationItemsAttribute()
  {
    if($this->default_item instanceof Item)
    {
      $ret_val = StockLocation::all()->map(function(StockLocation $sl) {

        $stock_location_item = RelStockLocationItem::where('item_id', $this->default_item_id)
          ->where('stock_location_id', $sl->id)->first();

        $local_ret_val = ['id' => $sl->id, 'name' => $sl->name, 'qty' => 0, 'active' => false];

        if(!empty($stock_location_item))
        {
          $local_ret_val['qty'] = $stock_location_item->quantity_available;
          $local_ret_val['active'] = !$stock_location_item->is_inactive;
        }

        return $local_ret_val;
      });
    }
    else
    {
      $ret_val = StockLocation::all()->map(function(StockLocation $sl) {
        return ['id' => $sl->id, 'name' => $sl->name, 'qty' => 0, 'active' => false];
      });
    }

    return $ret_val;
  }

  /** Returns the stock location items */
  public function getStockLocationItemsAttribute()
  {
    $ret_val = new Collection();
    $default_item = $this->default_item;
    if ($default_item instanceof Item)
    {
      $ret_val = $default_item->rel_stock_location_items;
    }

    return $ret_val;
  }

  public function getSkuAttribute()
  {
    return !empty($this->default_item) ? $this->default_item->sku : null;
  }

  public function rel_product_product_categories()
  {
    return $this->hasMany('App\RelProductProductCategory');
  }

  public function getExtraData(): array
  {
    $ret_val = [
      'categories' => ProductCategory::orderBy('name', 'asc')->get()
        ->filter(function (ProductCategory $pc)
        {
          return !$pc->is_inactive;
        })
        ->map(function (ProductCategory $pc)
        {
          return ['id' => $pc->id, 'label' => $pc->name];
        })
        ->values(),

      'vendors' => Vendor::orderBy('name', 'asc')->get()
        ->filter(function (Vendor $v)
        {
          return !$v->is_inactive;
        })
        ->map(function (Vendor $v)
        {
          return ['id' => $v->id, 'label' => $v->name];
        })
        ->values(),

      'stock_locations' => StockLocation::orderBy('name', 'asc')->get()
        ->map(function (StockLocation $sl)
        {
          return ['id' => $sl->id, 'label' => $sl->name];
        })
        ->values()
    ];

    return $ret_val;
  }

  /**
   * Get validation rules
   * @param array $data
   * @return array
   */
  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'name' => 'required',
      'slug' => 'required|unique:products,slug' . ($this->exists ? ",{$this->id}" : '') . '|alpha_dash',
      'store_price'            => 'required|numeric',
      'list_price'             => 'numeric_if_exists',
      'cost'                   => 'numeric_if_exists',
      'sku'                    => 'required|unique:items,sku' . (($this->exists && !empty($this->default_item_id)) ? ",{$this->default_item_id}" : '') . '|alpha_dash',
      'upc'                    => 'unique_if_exists:items,upc' . (($this->exists && !empty($this->default_item_id)) ? ",{$this->default_item_id}" : '') . '|numeric_if_exists',
      'quantity'               => 'numeric_if_exists',
      'weight'                 => 'required|numeric',
      'vendor_id'              => 'required|numeric',
      'categories'             => 'required'
    ];

    return $validation_rules;
  }

  public function getDefaultImageUrlAttribute()
  {
    $default_image = null;
    $url = '';
    if(!empty($this->default_image))
    {
      $default_image = $this->default_image;
    }
    else
    {
      if(!empty($this->images))
      {
        $default_image = $this->images[0];
      }
    }

    if(!empty($default_image))
      $url = $this->getImageUrl($default_image);

    return $url;
  }

  /**
   * Remove image from disk
   * @param string $file_name
   */
  public function deleteImageFromDisk(string $file_name)
  {
    if(file_exists(env('APP_ROOT') . '/storage/app/public/content-img/products/' . $this->id . '/' . $file_name))
      unlink(env('APP_ROOT') . '/storage/app/public/content-img/products/' . $this->id . '/' . $file_name);
  }

  /**
   * Get validation messages
   * @return array
   */
  public function getValidationMessages(): array
  {
    return [
      'vendor_id.numeric'              => 'Please make sure you select a vendor.',
      'main_stock_location_id.numeric' => 'Please make sure you select a stock location',
      'categories.required'            => 'You must add at least one category'
    ];
  }

  /**
   * Converts a product to a Solr document
   * @param DocumentInterface $doc
   * @return DocumentInterface
   */
  public function toSolrDocument(DocumentInterface $doc): DocumentInterface
  {
    $doc->id = $this->id;
    $doc->name = $this->name;
    $doc->skus = $this->items->map(function(Item $i) { return $i->sku; })->toArray();
    $doc->store_price = $this->getStorePriceAttribute();
    $doc->slug = $this->slug;
    $doc->sales = $this->getTotalSales();
    $doc->description = $this->description;
    $doc->tags = $this->tags;
    $doc->list_price = $this->getListPriceAttribute();
    $doc->upc = $this->getUpcAttribute();
    $doc->in_stock = true;
    $doc->is_inactive = $this->is_inactive;
    $doc->categories = $this->rel_product_product_categories->map(function(RelProductProductCategory $rppc) {
      return $rppc->product_category->name; })->toArray();
    $doc->category_ids = $this->rel_product_product_categories->map(function(RelProductProductCategory $rppc) {
      return $rppc->product_category->id; })->toArray();
    $doc->brand = $this->vendor->name;
    $doc->image = $this->getDefaultImageUrlAttribute();
    $doc->timestamp = $this->created_at->timestamp;

    return $doc;
  }

  public function getNonDefaultItemsAttribute()
  {
    if(empty($this->non_default_items_list))
    $this->non_default_items_list = $this->items->filter(function (Item $itm)
    {
      return !$itm->is_default;
    });

    return $this->non_default_items_list;
  }

  /**
   * Override the normal delete method
   */
  public function delete()
  {
    // Delete product category relationships
    RelProductProductCategory::where('product_id', $this->id)->delete();

    // Check if the default item isn't being used on a transaction
    if(TransactionLineItem::where('item_id', $this->default_item_id)->count() > 0)
      throw new \Exception('Cannot delete product because it is being used on a sales order or cart.');

    $this->default_item_id = null;
    $this->save();

    // Delete all items under product
    try
    {
      /** @var Item $item */
      foreach($this->items as $item)
      {
        $item->delete();
      }

      // Delete product images
      try
      {
        foreach($this->images as $image)
        {
          $this->deleteImageFromDisk($image);
        }
      }
      catch(\Exception $exception) {}

      // Delete the product
      Product::where('id', $this->id)->delete();
    }
    catch(\Exception $e)
    {
      throw new \Exception('Could not delete product: ' . $e->getMessage());
    }
  }
}
