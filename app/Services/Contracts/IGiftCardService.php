<?php
/**
 * The IGiftCardService interface definition.
 *
 * GiftCardService Contract
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

/**
 * Interface IGiftCardService
 * @package App\Services\Contracts
 */
interface IGiftCardService
{
  /**
   * Generate a random card number that's available
   * @param int $digits
   * @return string
   */
  public function generateNumber(int $digits = 10): string;

  /**
   * Get gift card balance
   * @param string $email
   * @param string $number
   * @return string
   */
  public function getGiftCardBalance(string $email, string $number);
}