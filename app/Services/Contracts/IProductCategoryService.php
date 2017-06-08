<?php
/**
 * The IProductCategoryService interface definition.
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface IProductCategoryService
{
  /**
   * Get filtered category listing data for product listing page
   * @param ProductCategory $product_category
   * @return Collection
   */
  public function getFilteredSubCategoriesForListing(ProductCategory $product_category);

  /**
   * Find a single category by slug
   * @param string $slug
   * @return ProductCategory
   */
  public function findBySlug(string $slug);

  /**
   * Upload banner or image
   * @param string $type
   * @param $id
   * @param Request $request
   * @return ProductCategory
   */
  public function uploadImage(string $type, $id, $request): ProductCategory;

  /**
   * Delete the banner or image
   * @param string $type
   * @param int $id
   */
  public function deleteImage(string $type, $id);

  /**
   * Formats list of categories for admin list view
   * @return Collection
   */
  public function getCategoriesForListView(): Collection;
}