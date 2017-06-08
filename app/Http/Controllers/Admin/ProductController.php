<?php
/**
 * The ProductController class definition.
 *
 * The admin controller for various product related functions
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class ProductController
 * @package App\Http\Controllers\Admin
 */
class ProductController extends Controller
{
  /** @var IProductService */
  protected $productService;

  /**
   * ProductController constructor.
   * @param IProductService $productService
   */
  public function __construct(IProductService $productService)
  {
    $this->productService = $productService;
  }

  /**
   * Uploads product photos
   * @param Request $request
   * @param int $id
   * @return array
   */
  public function upload_photos(Request $request, int $id)
  {
    try
    {
      $product = $this->productService->uploadProductPhotos($id, $request);
      $ret_val = ['errors' => false, 'images' => $product->view_images, 'default_image' => $product->default_image];
    }
    catch(\Exception $e)
    {
      $ret_val = ['errors' => $e->getMessage()];
    }

    return $ret_val;
  }

  /**
   * Delete the item image
   * @param int $id
   * @return array
   */
  public function delete_item_image(int $id)
  {
    $ret_val = ['errors' => false];

    $this->productService->deleteItemPhoto($id);
    return $ret_val;
  }

  /**
   * Update the main image
   * @param Request $request
   * @param int $id
   * @return array
   */
  public function update_main_image(Request $request, int $id)
  {
    try
    {
      $product = $this->productService->uploadMainImage($id, $request);
      $ret_val = ['errors' => false, 'product_id' => $product->id];
    }
    catch(\Exception $ex)
    {
      $ret_val = ['errors' => $ex->getMessage()];
    }

    return $ret_val;
  }

  /**
   * Upload item image
   * @param Request $request
   * @param int $id
   * @return array
   */
  public function upload_item_image(Request $request, int $id)
  {
    $ret_val = ['errors' => false];

    try
    {
      if($request->hasFile('file'))
      {
        $file_name = strtolower(preg_replace('/\s+/', '_', $request->file('file')->getClientOriginalName()));
        $item = $this->productService->uploadItemImage($id, $request);

        $ret_val = ['errors' => false, 'url' => $item->getImageUrl($file_name)];
      }
    }
    catch(\Exception $ex)
    {
      $ret_val = ['errors' => $ex->getMessage()];
    }

    return $ret_val;
  }

  /**
   * Delete an image from the product
   * @param Request $request
   * @param int $id
   * @return array
   */
  public function delete_image(Request $request, int $id)
  {
    try
    {
      $this->productService->deleteProductPhoto($id, $request->get('removed_image'), $request->get('new_main_image'));
      $ret_val = ['errors' => false];
    }
    catch(\Exception $ex)
    {
      $ret_val = ['errors' => $ex->getMessage()];
    }

    return $ret_val;
  }

  /**
   * Generate sku for item from product
   * @param int $product_id
   * @return array
   */
  public function generate_sku(int $product_id)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $ret_val['sku'] = $this->productService->generateItemSkuFromProduct($product_id);
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }
}