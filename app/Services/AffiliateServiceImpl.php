<?php
/**
 * The AffiliateServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Affiliate;
use App\Services\Contracts\IAffiliateService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Class AffiliateServiceImpl
 * @package App\Services
 */
class AffiliateServiceImpl implements IAffiliateService
{

  /**
   * Find a affiliates by slug
   * @param string $slug
   * @param bool $admin
   * @return mixed
   */
  public function findActiveAffiliate(string $slug, bool $admin = false)
  {
    if($admin)
    {
      $ret_val = Affiliate::whereRaw('lower(slug) = lower(?)', [$slug])->first();
    }
    else
    {
      $ret_val = Affiliate::whereRaw('lower(slug) = lower(?) AND is_inactive = ?', [$slug, false])->first();
    }

    return $ret_val;
  }

  /**
   * Upload image to affiliate
   * @param int $id
   * @param UploadedFile $file
   * @return array
   * @throws \Exception
   */
  public function uploadImageToAffiliate(int $id, UploadedFile $file): array
  {
    $ret_val = ['errors' => false];
    $affiliate = Affiliate::find($id);

    // Move file
    $file_name = strtolower(preg_replace('/\s+/', '_', $file->getClientOriginalName()));
    $path = $file->storeAs($id, $file_name, 'affiliate_images');

    if($path)
    {
      // Save file to database, only add image if it isn't already added
      if(empty($affiliate->images) || !in_array($file_name, $affiliate->images))
      {
        $images = $affiliate->images ?? [];
        $images[] = $file_name;

        // Set main image if there isn't one set or it isn't in the list of images
        if(empty($affiliate->main_image) && !empty($images))
        {
          $affiliate->setAttribute('main_image', $images[0]);
        }

        $affiliate->images = $images;
        $affiliate->save();

        $ret_val = ['images' => array_values($affiliate->images), 'main_image' => $affiliate->main_image, 'errors' => false];
      }
    }
    else
    {
      throw new \Exception('An error occurred while moving the uploaded file to the correct directory on the server.');
    }

    return $ret_val;
  }

  /**
   * Remove image
   * @param int $id
   * @param string $removed_image
   * @param string $new_main_image
   * @return array
   */
  public function deleteImage(int $id, string $removed_image, string $new_main_image = null): array
  {
    // Delete the image
    /** @var Affiliate $affiliate */
    $affiliate = Affiliate::find($id);
    $images = $affiliate->images;

    $affiliate->deleteImageFromDisk($removed_image);
    $key = array_search($removed_image, $images);
    unset($images[$key]);

    $affiliate->images = $images;

    if(!empty($new_main_image) && $new_main_image != $removed_image)
      $affiliate->main_image = $new_main_image;

    // Find main images
    if(count($images) > 0 || empty($affiliate->main_image))
    {
      $affiliate->main_image = current($images);
    }
    else
    {
      $affiliate->main_image = null;
    }

    $affiliate->save();
    $ret_val = ['errors' => false, 'images' => array_values($images), 'main_image' => $affiliate->main_image];

    return $ret_val;
  }

  /**
   * Update main image of affiliate
   * @param int $id
   * @param string $main_image_file
   * @return array
   */
  public function updateMainImage(int $id, string $main_image_file): array
  {
    $ret_val = ['errors' => false];
    Affiliate::find($id)->update(['main_image' => $main_image_file]);

    return $ret_val;
  }

  /**
   * Get all active
   * @param bool $is_admin
   * @return Collection
   */
  public function getActiveVloggers(bool $is_admin = false): Collection
  {
    $query = Affiliate::where('type', '=', 'vlogger')
      ->orderBy('created_at', 'desc');

    if(!$is_admin)
    {
      $query->where('is_inactive', '=', false);
    }

    return $query->get();
  }
}