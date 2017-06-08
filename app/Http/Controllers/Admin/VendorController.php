<?php
/**
 * The VendorController class definition.
 *
 * Admin controller for vendor records
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IVendorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class VendorController
 * @package App\Http\Controllers\Admin
 */
class VendorController extends Controller
{
  protected $vendorService;

  /**
   * VendorController constructor.
   * @param IVendorService $vendorService
   */
  public function __construct(IVendorService $vendorService)
  {
    $this->vendorService = $vendorService;
  }

  /**
   * Uploads an image to the vendor
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
      $vendor = $this->vendorService->uploadImage($id, $request);
      $ret_val = ['system_error' => false, 'file_name' => $vendor->image, 'href' => $vendor->getImageUrl($vendor->image)];
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Deletes the image from the vendor
   * @param int $id
   * @return array
   */
  public function delete_image(int $id)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $this->vendorService->deleteImage($id);
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }
}