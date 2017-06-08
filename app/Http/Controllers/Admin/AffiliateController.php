<?php
/**
 * The AffiliateController class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IAffiliateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class AffiliateController
 * @package App\Http\Controllers\Admin
 */
class AffiliateController extends Controller
{
  protected $affiliateService;

  /**
   * AffiliateController constructor.
   * @param IAffiliateService $affiliateService
   */
  public function __construct(IAffiliateService $affiliateService)
  {
    $this->affiliateService = $affiliateService;
  }

  /**
   * Uploads image to affiliate record
   * @param int $id
   * @param Request $request
   * @return array
   */
  public function upload_image(int $id, Request $request)
  {
    $allowed_mime_types = ['image/png', 'image/gif', 'image/jpeg'];
    $ret_val = ['errors' => false];

    try
    {
      if($request->hasFile('file'))
      {
        // Check file extension
        if(in_array($request->file('file')->getMimeType(), $allowed_mime_types))
        {
          $ret_val = $this->affiliateService->uploadImageToAffiliate($id, $request->file('file'));
        }
      }
    }
    catch(\Exception $e)
    {
      $ret_val = ['errors' => $e->getMessage()];
    }

    return $ret_val;
  }

  /**
   * Update the profile image on affiliate
   * @param int $id
   * @param Request $request
   * @return array
   */
  public function update_main_image(int $id, Request $request)
  {
    return $this->affiliateService->updateMainImage($id, $request->get('main_image'));
  }

  /**
   * Delete image from affiliate
   * @param int $id
   * @param Request $request
   * @return array
   */
  public function delete_image(int $id, Request $request)
  {
    return $this->affiliateService->deleteImage($id, $request->get('removed_image'), $request->get('new_main_image'));
  }
}