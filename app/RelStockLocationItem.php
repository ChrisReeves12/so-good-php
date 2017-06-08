<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RelStockLocationItem
 *
 * @property int $id
 * @property int $item_id
 * @property int $stock_location_id
 * @property int $quantity_available
 * @property bool $can_preorder
 * @property bool $is_inactive
 * @property int $quantity_reserved
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Item $item
 * @property-read \App\StockLocation $stock_location
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereCanPreorder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereIsInactive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereQuantityAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereQuantityReserved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereStockLocationId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelStockLocationItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RelStockLocationItem extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function stock_location()
    {
        return $this->belongsTo('App\StockLocation');
    }
}
