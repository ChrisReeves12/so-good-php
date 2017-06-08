<?php
/**
 * The GiftCardServiceImpl class definition.
 *
 * Default GiftCardService implementation
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\GiftCard;
use App\Services\Contracts\IGiftCardService;

/**
 * Class GiftCardServiceImpl
 * @package App\Services
 */
class GiftCardServiceImpl implements IGiftCardService
{
  /**
   * Generate a random card number that's available
   * @param int $digits
   * @return string
   */
  public function generateNumber(int $digits = 10): string
  {
    $done = false;
    $number = '';

    while(!$done)
    {
      $number = '9';
      for($x = 0; $x <= ($digits - 2); $x++)
      {
        $number .= mt_rand(0, 9);
      }

      // Check if number is available
      if(GiftCard::where('number', $number)->count() == 0)
        $done = true;
    }

    return $number;
  }

  /**
   * Get gift card balance
   * @param string $email
   * @param string $number
   * @return string
   * @throws \Exception
   */
  public function getGiftCardBalance(string $email, string $number)
  {
    $gift_card = GiftCard::whereRaw('is_inactive != ? AND number = ? AND lower(email) = lower(?)', [true, $number, $email])->first();
    if(!($gift_card instanceof GiftCard))
    {
      throw new \Exception('The gift card number and email combination entered is invalid.');
    }

    return number_format($gift_card->balance, 2);
  }
}