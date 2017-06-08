<?php

namespace App;

use App\Services\StockLocationService;

/**
 * App\StockLocation
 *
 * @property int $id
 * @property string $name
 * @property string $phone_number
 * @property string $address_id
 * @property bool $is_dropship
 * @property bool $is_main_location
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Address $address
 * @property-read mixed $display_record_name
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereIsDropship($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereIsMainLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation wherePhoneNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StockLocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockLocation extends AbstractRecordType
{
  protected $guarded = [];

  public function getViewParameters(): array
  {
    return ['name', 'phone_number', 'is_dropship', 'is_main_location', 'address', 'id'];
  }

  public function getDisplayRecordNameAttribute()
  {
    return $this->name;
  }

  public function getValidationRules($data = []): array
  {
    return [
      'name' => 'required'
    ];
  }

  public function delete()
  {
    $address_id = $this->address_id;
    if(!empty($address_id))
    {
      $this->setAttribute('address_id', $address_id);
      Address::where('id', $address_id)->delete();
    }

    RelStockLocationItem::where('stock_location_id', $this->id)->delete();
    StockLocation::where('id', $this->id)->delete();
  }

  public function address()
  {
    return $this->belongsTo('App\Address');
  }
}
