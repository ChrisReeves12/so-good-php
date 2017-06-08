<?php
/**
 * The VendorServiceImpl class definition.
 *
 * The default vendor service
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Address;
use App\Services\Contracts\ICRUDRecordTypeService;
use App\Services\Contracts\IVendorService;
use App\Vendor;
use Illuminate\Http\Request;

/**
 * Class VendorServiceImpl
 * @package App\Services
 */
class VendorServiceImpl implements IVendorService, ICRUDRecordTypeService
{
  /**
   * Create or update record in database
   * @param $vendor
   * @param array $data
   * @return array
   */
  public function createUpdate($vendor, array $data = [])
  {
    $ret_val = ['errors' => [], 'system_error' => false];

    // Check if address should be validated
    $should_add_address = false;
    foreach($data['data']['address'] as $value)
    {
      if(!empty($value))
      {
        $should_add_address = true;
        break;
      }
    }

    if($should_add_address)
    {
      // Validate shipping address
      if($vendor->address instanceof Address)
      {
        $address_errors = [];
        $address_valid = $vendor->address->validate($data['data']['address'], $address_errors);
      }
      else
      {
        $address_errors = [];
        $address = new Address($data['data']['address']);
        $address_valid = $address->validate($data['data']['address'], $address_errors);
      }

      if(!$address_valid)
      {
        foreach($address_errors as $address_error)
        {
          $ret_val['errors'][] = ['address.' . key($address_error) => array_values($address_error)];
        }
      }
      else
      {
        // Update address
        if(!($vendor->address instanceof Address) && (!empty($address)))
        {
          $address->save();
          $data['data']['address_id'] = $address->id;
        }
        else
        {
          $vendor->address->update($data['data']['address']);
        }
      }
    }
    else
    {
      if($vendor->address instanceof Address)
        $vendor->address->delete();
    }

    // Validate vendor
    unset($data['data']['address']);
    $vendor_errors = [];
    $is_valid = $vendor->validate($data['data'], $vendor_errors);
    if(!$is_valid)
    {
      foreach($vendor_errors as $error)
      {
        $ret_val['errors'][] = [key($error) => array_values($error)];
      }
    }

    if(empty($ret_val['errors']))
    {
      if($vendor->exists)
      {
        $vendor->update($data['data']);
      }
      else
      {
        $vendor->fill($data['data']);
        $vendor->save();
      }

      $ret_val['id'] = $vendor->id;
    }

    return $ret_val;
  }

  /**
   * Upload image for vendor
   * @param int $vendor_id
   * @param Request $request
   * @return Vendor
   * @throws \Exception
   */
  public function uploadImage($vendor_id, $request)
  {
    // Find the vendor
    $vendor = Vendor::find($vendor_id);
    if(!($vendor instanceof Vendor))
      throw new \Exception('Could not find vendor with id: ' . $vendor_id);

    $allowed_mime_types = ['image/png', 'image/gif', 'image/jpeg'];

    if(!$request->hasFile('file'))
      throw new \Exception('No file uploaded...');

    if(!in_array($request->file('file')->getMimeType(), $allowed_mime_types))
      throw new \Exception('The file uploaded must be a PNG, GIF, or JPEG...');

    $file_name = strtolower(preg_replace('/\s+/', '_', $request->file('file')->getClientOriginalName()));
    $path = $request->file('file')->storeAs($vendor_id, $file_name, 'vendor_images');

    if(!$path)
      throw new \Exception('An error occurred while moving uploaded file to storage.');

    // Save file to vendor
    $vendor->image = $file_name;
    $vendor->save();

    return $vendor;
  }

  /**
   * Delete image from vendor record
   * @param $vendor_id
   * @throws \Exception
   */
  public function deleteImage($vendor_id)
  {
    // Find the vendor
    $vendor = Vendor::find($vendor_id);
    if(!($vendor instanceof Vendor))
      throw new \Exception('Could not find vendor with id: ' . $vendor_id);

    // Remove the image
    if($vendor->image)
      $vendor->deleteImageFromDisk($vendor->image);
    $vendor->image = null;

    $vendor->save();
  }
}