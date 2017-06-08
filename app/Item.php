<?php

namespace App;

use App\Services\ItemService;
use DB;

class Item extends AbstractRecordType
{
  protected $guarded = [];
  protected $casts = ['details' => 'array'];
  protected $sales_count;

  public function rel_stock_location_items()
  {
    return $this->hasMany('App\RelStockLocationItem');
  }

  public function product()
  {
    return $this->belongsTo('App\Product');
  }

  public function getReadOnlyParams(): array
  {
    return ['calculated_stock_status'];
  }

  public function getTotalSales()
  {
    if(!isset($this->sales_count))
    {
      $sales_count = 0;

      $lines = DB::table('transaction_line_items')
        ->join('sales_orders', 'sales_orders.transaction_id', '=', 'transaction_line_items.transaction_id')
        ->where('transaction_line_itemable_type', 'SalesOrder')
        ->where('transaction_line_items.item_id', $this->id)
        ->get();

      if($lines->count() > 0)
      {
        foreach($lines as $line)
        {
          $sales_count += $line->quantity;
        }
      }

      $this->sales_count = $sales_count;
    }

     return $this->sales_count;
  }

  public function getPostSaveFields(): array
  {
    return ['stock_locations'];
  }

  public function setStockLocationsAttribute($values)
  {
    foreach ($values as $value)
    {
      $is_inactive = ($value['active'] != 'true');

      // Attempt to find the RelStockLocation
      $rel_stock_location = RelStockLocationItem::whereRaw('item_id = ? AND stock_location_id = ?', [$this->id, $value['id']])->first();
      if($rel_stock_location instanceof RelStockLocationItem)
      {
        $rel_stock_location->update(['quantity_available' => $value['qty'], 'is_inactive' => $is_inactive]);
      }
      else
      {
        $this->rel_stock_location_items()->create([
          'item_id'            => $this->id,
          'stock_location_id'  => $value['id'],
          'quantity_available' => $is_inactive ? 0 : $value['qty'],
          'is_inactive'        => $is_inactive
        ]);
      }
    }
  }

  /**
   * Calculates the stock status based on location of all active locations
   */
  public function calculateStockStatus()
  {
    $rel_stock_locations = $this->rel_stock_location_items->filter(function(RelStockLocationItem $rsli) {
      return !$rsli->is_inactive;
    });

    $total_quantity_available = 0;

    /** @var RelStockLocationItem $rel_stock_location */
    foreach($rel_stock_locations as $rel_stock_location)
    {
      $total_quantity_available += $rel_stock_location->quantity_available;
    }

    $this->calculated_stock_status = ($total_quantity_available > 0) ? 'in_stock' : 'out_of_stock';

    return $this->calculated_stock_status;
  }

  public function findLocationsThatCanFulfillQuantity($quantity)
  {
    $stock_location_items = $this->rel_stock_location_items()
      ->whereRaw('is_inactive = false AND quantity_available >= ?', [$quantity])
      ->orderBy('quantity_available', 'desc')
      ->get();

    return $stock_location_items;
  }

  public function getIdealStockLocation($quantity = 1)
  {
    $ideal_location = $this->findLocationsThatCanFulfillQuantity($quantity)->first()->stock_location;
    return $ideal_location;
  }

  public function getStockLocationsAttribute()
  {
    return $this->rel_stock_location_items->map(function(RelStockLocationItem $rsli) {
      return [
        'id' => $rsli->stock_location_id,
        'qty' => $rsli->quantity_available,
        'name' => $rsli->stock_location->name,
        'active' => !$rsli->is_inactive
      ];
    });
  }

  public function getUrlAttribute()
  {
    return route('page', ['slug' => $this->product->slug]);
  }

  /**
   * Find a product from the given options
   * @param int $product_id
   * @param array $product_options
   * @param Item
   */
  static public function findFromOptions(int $product_id, array $product_options)
  {
    $product = Product::whereRaw('id = ? AND is_inactive = ?', [$product_id, false])->firstOrFail();

    $item_result = $product->non_default_items->filter(function(Item $item) use($product_options) {
      $details = $item->details;
      if(!empty($details))
      {
        $item_details_array = [];
        foreach($details as $detail)
        {
          $key = trim(strtolower($detail['key']));
          $value = trim(strtolower($detail['value']));
          $item_details_array[$key] = $value;
        }
      }

      $diffs = array_diff($product_options, $item_details_array);
      $ret_val = empty($diffs);

      return $ret_val;
    })->first();

    return $item_result;
  }

  /**
   * Returns the total quantity at all active stock locations
   */
  public function getTotalQuantityAttribute()
  {
    $quantity = 0;

    /** @var RelStockLocationItem $rel_stock_location_item */
    foreach ($this->rel_stock_location_items as $rel_stock_location_item)
    {
      if ($rel_stock_location_item->is_inactive)
      {
        continue;
      }

      $quantity += $rel_stock_location_item->quantity_available;
    }

    return $quantity;
  }

  public function getViewStockLocationsAttribute()
  {
    return $this->rel_stock_location_items->map(function(RelStockLocationItem $rsli) {
      return [
        'id' => $rsli->stock_location_id,
        'active' => !$rsli->is_inactive,
        'name' => $rsli->stock_location->name,
        'qty' => $rsli->quantity_available
      ];
    });
  }

  public function main_stock_location()
  {
    return $this->hasOne('App\StockLocation', 'id', 'main_stock_location_id');
  }

  public function getDisplayRecordNameAttribute()
  {
    return $this->sku . ': ' . $this->product->name;
  }

  public function getViewParameters(): array
  {
    return ['sku', 'product_id', 'ean', 'stock_status_override', 'list_price', 'store_price',
      'stock_locations', 'is_inactive', 'weight', 'cost', 'view_image', 'id', 'upc',
      'isbn', 'product_name', 'calculated_stock_status', 'ships_alone', 'details'];
  }

  public function getProductNameAttribute()
  {
    return $this->sku . ': ' . $this->product->name;
  }

  public function getImageUrl($file_name)
  {
    return ('/content-img/products/items/' . $this->id . '/' . $file_name);
  }

  public function getViewImageAttribute()
  {
    $ret_val = null;

    if(!empty($this->attributes['image']))
    {
      $ret_val = [
        'file_name' => $this->attributes['image'],
        'url'       => $this->getImageUrl($this->attributes['image'])
      ];
    }

    return $ret_val;
  }

  /**
   * Returns how many of this item can be ordered from one location
   */
  public function getSingleOrderableQuantityAttribute()
  {
    $highest_rel_stock_location_item = $this->rel_stock_location_items()
      ->where('is_inactive', false)
      ->where('quantity_available', '>', 0)
      ->orderBy('quantity_available', 'desc')
      ->first();

    return (!empty($highest_rel_stock_location_item) ? $highest_rel_stock_location_item->quantity_available : 0);
  }

  public function getQuantityAvailableAttribute()
  {
    $quantity_avail = 0;
    $rel_stock_location_items = $this->rel_stock_location_items()->where('is_inactive', false)->get();

    foreach($rel_stock_location_items as $rel_stock_location_item)
    {
      $quantity_avail += $rel_stock_location_item->quantity_available ?? 0;
    }

    return $quantity_avail;
  }

  /**
   * Delete image from disk
   * @param string $file_name
   */
  public function deleteImageFromDisk(string $file_name)
  {
    if(file_exists(env('APP_ROOT') . '/storage/app/public/content-img/products/items/' . $this->id . '/' . $file_name))
      unlink(env('APP_ROOT') . '/storage/app/public/content-img/products/items/' . $this->id . '/' . $file_name);
  }

  /**
   * Override the delete method to remove dependencies
   */
  public function delete()
  {
    if(TransactionLineItem::where('item_id', $this->id)->count() > 0)
      throw new \Exception('Cannot delete item ' . $this->id . ' because it exists on a sales order or cart.');

    // Delete images from disk
    if(!empty($this->attributes['image']))
      $this->deleteImageFromDisk($this->attributes['image']);

    RelStockLocationItem::where('item_id', $this->id)->delete();
    Item::where('id', $this->id)->delete();
  }

  /**
   * Get validation rules
   * @return array
   */
  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'store_price'     => 'required|numeric',
      'cost'            => 'numeric_if_exists',
      'list_price'      => 'numeric_if_exists',
      'sku'             => 'required|unique:items,sku' . ($this->exists ? ",{$this->id}" : '') . '|alpha_dash',
      'upc'             => 'unique_if_exists:items,upc' . ($this->exists ? ",{$this->id}" : '') . '|numeric_if_exists',
      'weight'          => 'required|numeric',
      'stock_locations' => 'required',
      'details'         => 'required|array|item_unique_detail_names|item_unique_details:' . $data['product_id'] . ($this->exists ? ",{$this->id}" : '')
    ];

    return $validation_rules;
  }

  public function getValidationMessages(): array
  {
    return [
      'details.required'         => 'At least one item attribute is required.',
      'details.array'            => 'Item attributes are blank or not in correct format, please contact system administrator.',
      'stock_locations.required' => 'There are no stock locations associated with this item, stock locations are required.'
    ];
  }
}
