<?php
/**
 * The PopupController class definition.
 *
 * Handles various functions for displaying popups
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\IPopupService;
use App\Http\Controllers\Controller;
use App\Popup;
use Illuminate\Http\Request;

/**
 * Class PopupController
 * @package App\Http\Controllers\Frontend
 */
class PopupController extends Controller
{
  protected $popupService;

  /**
   * PopupController constructor.
   * @param IPopupService $popupService
   */
  public function __construct(IPopupService $popupService)
  {
    $this->popupService = $popupService;
  }

  /**
   * Gets information and data of a popup to show on the frontend
   * @param Request $request
   * @return array
   */
  public function register(Request $request)
  {
    $internal_name = $request->get('internal_name');
    $popup = $this->popupService->findPopupByInternalName($internal_name);
    if(($popup instanceof Popup) && $this->popupService->shouldPopupShow($popup, $request->get('page_data')))
    {
      $domain = preg_replace('/(https?\:\/\/)/i', '', business('site_url'));
      setcookie($popup->cookie_name, '1',time() + (86400 * $popup->cookie_day_life),'/', $domain, false,true);
    }
    else
    {
      $popup = false;
    }

    return ['popup' => $popup ?? false];
  }
}