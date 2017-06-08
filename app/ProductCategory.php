<?php

namespace App;

/**
 * App\ProductCategory
 *
 * @property int $id
 * @property string $name
 * @property bool $is_inactive
 * @property int $parent_category_id
 * @property string $description
 * @property string $slug
 * @property string $tags
 * @property string $image
 * @property string $banner
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $child_categories
 * @property-read mixed $view_banner
 * @property-read mixed $view_image
 * @property-read \App\ProductCategory $parent_category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RelProductProductCategory[] $rel_product_product_categories
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereBanner($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereIsInactive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereParentCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereTags($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductCategory extends AbstractRecordType
{
  private $child_cats;

  public function getViewParameters(): array
  {
    return ['id', 'name', 'is_inactive', 'description', 'parent_category_id', 'slug', 'tags', 'view_image', 'view_banner'];
  }

  public function getExtraData(): array
  {
    $ret_val = [
      'parent_categories' => ProductCategory::orderBy('name', 'asc')->get()
        ->filter(function(ProductCategory $pc) { return((empty($this->id)) || $pc->id != $this->id); })
        ->map(function(ProductCategory $pc) { return [
          'id' => $pc->id,
          'label' => $pc->name
        ]; })->values()
    ];

    return $ret_val;
  }

  /**
   * Get child categories
   */
  public function getChildCategoriesAttribute()
  {
    if(empty($this->child_cats))
      $this->child_cats = ProductCategory::where('parent_category_id', $this->id)->get();

    return $this->child_cats;
  }

  public function rel_product_product_categories()
  {
    return $this->hasMany('App\RelProductProductCategory');
  }

  /**
   * Remove image from disk
   * @param string $file_name
   */
  public function deleteImageFromDisk(string $file_name)
  {
    if(file_exists(env('APP_ROOT') . '/storage/app/public/content-img/product-categories/' . $this->id . '/' . $file_name))
      unlink(env('APP_ROOT') . '/storage/app/public/content-img/product-categories/' . $this->id . '/' . $file_name);
  }

  public function getViewBannerAttribute()
  {
    $ret_val = null;

    if(!empty($this->attributes['banner']))
    {
      $ret_val = [
        'href'      => $this->getImageUrl($this->attributes['banner']),
        'file_name' => $this->attributes['banner']
      ];
    }

    return $ret_val;
  }

  public function getViewImageAttribute()
  {
    $ret_val = null;

    if(!empty($this->attributes['image']))
    {
      $ret_val = [
        'href'      => $this->getImageUrl($this->attributes['image']),
        'file_name' => $this->attributes['image']
      ];
    }

    return $ret_val;
  }

  public function getImageUrl(string $file_name)
  {
    return('/content-img/product-categories/' . $this->id . '/' . $file_name);
  }

  public function parent_category()
  {
    return $this->belongsTo('App\ProductCategory', 'parent_category_id', 'id');
  }

  public function delete()
  {
    // Do not allow categories to be deleted if products exist
    if(RelProductProductCategory::where('product_category_id', $this->id)->count() > 0)
      throw new \Exception('Cannot delete category because there are products in this category.');

    $this->parent_category()->dissociate();
    $this->rel_product_product_categories()->delete();
    ProductCategory::where('id', $this->id)->delete();
  }

  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'name' => 'required',
      'slug' => 'required|unique:product_categories,slug' . ($this->exists ? ",{$this->id}" : '') . '|alpha_dash',
    ];

    return $validation_rules;
  }
}
