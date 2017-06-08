<?php
/**
 * The IVendorService interface definition.
 *
 * The interface of vendor service
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\Vendor;
use Illuminate\Http\Request;

/**
 * Interface IVendorService
 * @package App\Services\Contracts
 */
interface IVendorService
{
  /**
   * Upload image for vendor
   * @param int $vendor_id
   * @param Request $request
   * @return Vendor
   */
  public function uploadImage($vendor_id, $request);

  /**
   * Delete image from vendor record
   * @param $vendor_id
   */
  public function deleteImage($vendor_id);
}