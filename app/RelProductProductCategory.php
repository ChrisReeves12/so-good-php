<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RelProductProductCategory
 *
 * @property int $id
 * @property int $product_id
 * @property int $product_category_id
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Product $product
 * @property-read \App\ProductCategory $product_category
 * @method static \Illuminate\Database\Query\Builder|\App\RelProductProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelProductProductCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelProductProductCategory whereProductCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelProductProductCategory whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelProductProductCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RelProductProductCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RelProductProductCategory extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function product_category()
    {
        return $this->belongsTo('App\ProductCategory');
    }
}
