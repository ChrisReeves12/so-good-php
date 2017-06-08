<?php
/**
 * The IPayPalService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\ShoppingCart;

interface IPayPalService
{
  /**
   * Send payment to PayPal
   * @param bool $use_sandbox
   * @return string
   */
  public function send_payment($use_sandbox = true);

  /**
   * Execute PayPal payment
   * @param string $payer_id
   * @param string $payment_id
   * @param bool $use_sandbox
   * @return array
   * @return
   */
  public function execute_payment($payer_id, $payment_id, $use_sandbox = true);

  /**
   * Gets whether or not to use the sandbox mode
   * @param string $test_order_flag
   * @return bool
   */
  public function usePayPalSandbox($test_order_flag): bool;
}