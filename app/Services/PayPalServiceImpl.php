<?php
/**
 * The PayPalServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\ICartService;
use App\Services\Contracts\IPayPalService;
use App\ShoppingCart;

/**
 * Class PayPalServiceImpl
 * @package App\Services
 */
class PayPalServiceImpl implements IPayPalService
{
  protected $cartService;

  /**
   * PayPalServiceImpl constructor.
   * @param ICartService $cartService
   */
  public function __construct(ICartService $cartService)
  {
    $this->cartService = $cartService;
  }

  /**
   * Send payment to PayPal
   * @param bool $use_sandbox
   * @return string
   * @throws \Exception
   */
  public function send_payment($use_sandbox = true)
  {
    $token_access_endpoint = 'https://api.' . ($use_sandbox ? 'sandbox.' : '') . 'paypal.com/v1/oauth2/token';
    $payment_api_endpoint = 'https://api.' . ($use_sandbox ? 'sandbox.' : '') . 'paypal.com/v1/payments/payment';

    // Get token
    $paypal_id = $use_sandbox ? business('dev_paypal_id') : business('paypal_id');
    $paypal_key = $use_sandbox ? business('dev_paypal_key') : business('paypal_key');

    $ch = curl_init($token_access_endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $paypal_id . ':' . $paypal_key);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    $result = curl_exec($ch);

    if(empty($result))
      throw new \Exception('An error occurred while contacting PayPal for authorization, did not get a response.');

    $response_array = json_decode($result, true);

    $access_token = $response_array['access_token'];
    session(['paypal_token' => $access_token]);
    $store_url = preg_replace('/https?\:\/\//i', '', business('site_url'));

    // Once we have a token, make payment request
    $payload = [
      'intent' => 'sale',
      'redirect_urls' => [
        'return_url' => 'http://'.$store_url.'/checkout',
        'cancel_url' => 'http://'.$store_url.'/checkout'
      ],
      'payer' => [
        'payment_method' => 'paypal'
      ],
      'transactions' => $this->_build_transaction_data($this->cartService->getCurrentCart())
    ];

    // Send request
    $ch = curl_init($payment_api_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $pay_api_results = curl_exec($ch);

    if(empty($pay_api_results))
      throw new \Exception('An error occurred while sending order to PayPal. Did not get a response.');

    $pay_api_results_array = json_decode($pay_api_results, true);
    return $pay_api_results_array['id'];
  }

  /**
   * @param $shopping_cart
   * @return array
   */
  private function _build_transaction_data($shopping_cart)
  {
    $sub_total = $shopping_cart->sub_total + (is_numeric($shopping_cart->discount_amount) ? $shopping_cart->discount_amount : 0);
    $sub_total = $sub_total - (is_numeric($shopping_cart->parent_transaction->gift_card_amount) ? $shopping_cart->parent_transaction->gift_card_amount : 0);

    return [0 => [
      'amount' => [
        'total' => number_format($shopping_cart->total, 2, '.', ''),
        'currency' => 'USD',
        'details' => [
          'subtotal' => number_format($sub_total, 2, '.', ''),
          'tax' => number_format($shopping_cart->tax, 2, '.', ''),
          'shipping' => number_format($shopping_cart->shipping_total, 2, '.', '')
        ]
      ]
    ]];
  }

  /**
   * Execute PayPal payment
   * @param string $payer_id
   * @param string $payment_id
   * @param bool $use_sandbox
   * @return array
   * @throws \Exception
   */
  public function execute_payment($payer_id, $payment_id, $use_sandbox = true)
  {
    $access_token = session('paypal_token');
    if(empty($access_token))
      throw new \Exception('Paypal token not in session.');

    $endpoint = 'https://api.' . ($use_sandbox ? 'sandbox.' : '') . 'paypal.com/v1/payments/payment/' . $payment_id . '/execute';

    $payload = [
      'payer_id' => $payer_id
    ];

    // Send request
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $results = curl_exec($ch);

    if(empty($results))
      throw new \Exception('An error occurred while executing PayPal purchase. Did not get a response.');

    return json_decode($results, true);
  }

  /**
   * Gets whether or not to use the sandbox mode
   * @param string $test_order_flag
   * @return bool
   */
  public function usePayPalSandbox($test_order_flag): bool
  {
    return (env('APP_ENV') != 'production' || $test_order_flag == 'true');
  }
}