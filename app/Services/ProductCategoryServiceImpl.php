<?php
/**
 * The ProductCategoryServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IProductCategoryService;
use App\ProductCategory;
use Illuminate\Support\Collection;

/**
 * Class ProductCategoryServiceImpl
 * @package App\Services
 */
class ProductCategoryServiceImpl implements IProductCategoryService
{
  /**
   * @param ProductCategory $product_category
   * @return Collection
   */
  public function getFilteredSubCategoriesForListing(ProductCategory $product_category)
  {
    $sub_categories = new Collection();

    if(!empty($product_category->child_categories))
    {
      $sub_categories = $product_category->child_categories
        ->filter(function(ProductCategory $cc) { return (!$cc->is_inactive); })
        ->map(function(ProductCategory $cc) { return ['id' => $cc->id, 'slug' => $cc->slug, 'name' => $cc->name]; })
        ->values();
    }

    return $sub_categories;
  }

  /**
   * Find a single category by slug
   * @param string $slug
   * @return ProductCategory
   */
  public function findBySlug(string $slug)
  {
    return ProductCategory::whereRaw('lower(slug) = ? AND is_inactive = ?', [strtolower($slug), false])->first();
  }

  /**
   * Upload an image or banner
   * @param string $type
   * @param int $id
   * @param \Illuminate\Http\Request $request
   * @return ProductCategory
   * @throws \Exception
   */
  public function uploadImage(string $type, $id, $request): ProductCategory
  {
    // Find the category
    $product_category = ProductCategory::find($id);
    if(!($product_category instanceof ProductCategory))
      throw new \Exception('Could not find product category with id: ' . $id);

    $allowed_mime_types = ['image/png', 'image/gif', 'image/jpeg'];

    if(!$request->hasFile('file'))
      throw new \Exception('No file uploaded...');

    if(!in_array($request->file('file')->getMimeType(), $allowed_mime_types))
      throw new \Exception('The file uploaded must be a PNG, GIF, or JPEG...');

    $file_name = strtolower(preg_replace('/\s+/', '_', $request->file('file')->getClientOriginalName()));
    $path = $request->file('file')->storeAs($id, $file_name, 'product_category_images');

    if(!$path)
      throw new \Exception('An error occurred while moving uploaded file to storage.');

    // Save file
    switch($type)
    {
      case 'image':
        $product_category->image = $file_name;
        break;

      case 'banner':
        $product_category->banner = $file_name;
        break;
    }

    $product_category->save();
    return $product_category;
  }

  /**
   * Delete the banner or image
   * @param string $type
   * @param int $id
   * @throws \Exception
   */
  public function deleteImage(string $type, $id)
  {
    // Find the category
    $product_category = ProductCategory::find($id);
    if(!($product_category instanceof ProductCategory))
      throw new \Exception('Could not find product category with id: ' . $id);

    // Remove the image
    switch($type)
    {
      case 'image':
        if($product_category->image)
          $product_category->deleteImageFromDisk($product_category->image);

        $product_category->image = null;
        break;

      case 'banner':
        if($product_category->banner)
          $product_category->deleteImageFromDisk($product_category->banner);

        $product_category->banner = null;
        break;
    }

    $product_category->save();
  }

  /**
   * Formats list of categories for admin list view
   * @return Collection
   */
  public function getCategoriesForListView(): Collection
  {
    return ProductCategory::whereNull('parent_category_id')
      ->orderBy('created_at', 'DESC')
      ->get()
      ->map(function(ProductCategory $pc) {
        $ret_val = $pc->toArray();
        $ret_val['children'] = $pc->child_categories->map(function(ProductCategory $cc) {
          $cc_ret_val = $cc->toArray();
          $cc_ret_val['child_count'] = $cc->child_categories->count();
          $cc_ret_val['image'] = $cc->view_image;
          return $cc_ret_val;
        })->toArray();

        $ret_val['image'] = $pc->view_image;
        return $ret_val;
      });
  }
}