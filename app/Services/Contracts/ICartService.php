<?php
/**
 * The ICartService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\Entity;
use App\ShoppingCart;
use App\Transaction;
use App\TransactionLineItem;
use Illuminate\Support\Collection;

interface ICartService
{
  /**
   * Do payment checkout
   * @param bool $test_order
   * @param array $paypal_data
   * @param array $card_data
   * @param string $marketing_channel
   * @return array
   */
  public function doCheckout(bool $test_order = false, array $paypal_data = [], array $card_data = [], string $marketing_channel): array;

  /**
   * Add item to shopping cart
   * @param int $current_user_id
   * @param array $option_values
   * @param int $product_id
   * @param int $quantity
   * @return array
   */
  public function add($current_user_id, $option_values, $product_id, $quantity = 1);

  /**
   * @param int $line_id
   */
  public function deleteLineItem($line_id);

  /**
   * Update line item quantity
   * @param $line_id
   * @param $quantity
   * @return TransactionLineItem
   */
  public function updateLineItemQuantity($line_id, $quantity): TransactionLineItem;

  /**
   * Update shipping method on cart
   * @param int $shipping_method_id
   */
  public function updateShippingMethod($shipping_method_id);

  /**
   * Update addresses
   * @param array $billing_address_data
   * @param array $shipping_address_data
   * @param bool $same_as_billing
   * @return array
   */
  public function updateAddresses($billing_address_data, $shipping_address_data, $same_as_billing): array;

  /**
   * Validate the checkout information
   * @return array
   */
  public function validateCheckoutForm(): array;

  /**
   * Updates user information on cart
   * @param array $user_data
   * @return array
   */
  public function updateUserInfo($user_data): array;

  /**
   * Update the discount code
   * @param string $discount_code
   * @param array $available_discount_codes
   * @return array
   */
  public function updateDiscountCode($discount_code, array $available_discount_codes): array;

  /**
   * Do inventory check against the shopping cart
   * @return array
   */
  public function doInventoryCheck(): array;

  /**
   * Get the current cart
   * @return ShoppingCart
   */
  public function getCurrentCart();

  /**
   * Update gift card data
   * @param ShoppingCart $shopping_cart
   * @param array $data
   * @return array
   */
  public function updateGiftCard(ShoppingCart $shopping_cart, array $data);

  /**
   * Get shipping methods and rates
   * @param Transaction $transaction
   * @return Collection
   */
  public function getShippingMethodData(Transaction $transaction);
}