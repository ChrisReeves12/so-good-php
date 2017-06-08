<?php
/**
 * The GiftCardController class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\IGiftCardService;

/**
 * Class GiftCardController
 * @package App\Http\Controllers\Admin
 */
class GiftCardController extends Controller
{
  protected $giftCardService;

  public function __construct(IGiftCardService $giftCardService)
  {
    $this->giftCardService = $giftCardService;
  }

  /**
   * Generate a random gift card number
   * @return array
   */
  public function generate_card_number()
  {
    return ['number' => $this->giftCardService->generateNumber()];
  }
}