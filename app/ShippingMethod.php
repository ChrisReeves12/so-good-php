<?php

namespace App;

/**
 * App\ShippingMethod
 *
 * @property int $id
 * @property string $name
 * @property bool $is_express
 * @property string $carrier_name
 * @property string $api_identifier
 * @property bool $is_inactive
 * @property int $transit_time
 * @property string $calculation_method
 * @property float $flat_rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $display_record_name
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereApiIdentifier($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereCalculationMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereCarrierName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereFlatRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereIsExpress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereIsInactive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereTransitTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ShippingMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShippingMethod extends AbstractRecordType
{
  public function getViewParameters(): array
  {
    return ['name', 'id', 'api_identifier', 'transit_time', 'carrier_name', 'calculation_method', 'is_express', 'flat_rate', 'is_inactive'];
  }

  public function getValidationRules($data = []): array
  {
    return [
      'name' => 'required',
      'api_identifier' => 'required',
      'transit_time' => 'required|numeric'
    ];
  }

  public function canBeDuplicated()
  {
    return true;
  }

  public function getDisplayRecordNameAttribute()
  {
    return $this->name;
  }
}
