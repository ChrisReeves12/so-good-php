<?php
/**
 * The ProductCategoryController class definition.
 *
 * This controller manages various tasks to be done on product categories.
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IProductCategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class ProductCategoryController
 * @package App\Http\Controllers\Admin
 */
class ProductCategoryController extends Controller
{
  protected $productCategoryService;

  /**
   * ProductCategoryController constructor.
   * @param IProductCategoryService $productCategoryService
   */
  public function __construct(IProductCategoryService $productCategoryService)
  {
    $this->productCategoryService = $productCategoryService;
  }

  /**
   * Uploads an image to the product category
   * @param Request $request
   * @param int $id
   * @return array
   * @throws \Exception
   */
  public function upload_image(Request $request, int $id)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $product_category = $this->productCategoryService->uploadImage('image', $id, $request);
      $ret_val = ['system_error' => false, 'file_name' => $product_category->image, 'href' => $product_category->getImageUrl($product_category->image)];
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Uploads a banner to the product category
   * @param Request $request
   * @param int $id
   * @return array
   * @throws \Exception
   */
  public function upload_banner(Request $request, int $id)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $product_category = $this->productCategoryService->uploadImage('banner', $id, $request);
      $ret_val = ['system_error' => false, 'file_name' => $product_category->banner, 'href' => $product_category->getImageUrl($product_category->banner)];
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Deletes the image from the product category
   * @param int $id
   * @return array
   */
  public function delete_image(int $id)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $this->productCategoryService->deleteImage('image',$id);
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Deletes a banner from the product category
   * @param int $id
   * @return array
   */
  public function delete_banner(int $id)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $this->productCategoryService->deleteImage('banner',$id);
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Show categories listings
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function list()
  {
    return view('admin.product_category.list', ['parent_categories' => $this->productCategoryService->getCategoriesForListView()]);
  }
}