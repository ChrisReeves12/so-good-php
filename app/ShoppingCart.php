<?php

namespace App;

use App\Services\Contracts\ICartService;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ShoppingCart
 *
 * @property int $id
 * @property int $transaction_id
 * @property array $discount_codes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Illuminate\Support\Collection $line_items
 * @property float $sub_total
 * @property float $shipping_total
 * @property int $selected_shipping_method_id
 * @property \App\Transaction $parent_transaction
 * @property float $total
 * @property string $email
 * @property int $item_count
 * @property float $discount_amount
 * @property float $tax
 * @property \App\Address $billing_address
 * @property \App\Address $shipping_address
 * @mixin \Eloquent
 */

class ShoppingCart extends Model
{
  protected $appends = ['item_count', 'sub_total'];
  protected $hidden = ['parent_transaction'];
  protected $casts = ['discount_codes' => 'array'];

  public function parent_transaction()
  {
    return $this->belongsTo('App\Transaction', 'transaction_id', 'id');
  }

  public function getLineItemsAttribute()
  {
    return $this->parent_transaction->transaction_line_items;
  }

  public function getSubTotalAttribute()
  {
    return $this->parent_transaction->sub_total;
  }

  public function getShippingTotalAttribute()
  {
    return $this->parent_transaction->shipping_total ?? 0;
  }

  public function getTotalAttribute()
  {
    return $this->parent_transaction->total;
  }

  public function getDiscountAmountAttribute()
  {
    return $this->parent_transaction->discount_amount;
  }

  public function getTaxAttribute()
  {
    return $this->parent_transaction->tax ?? 0;
  }

  public function getBillingAddressAttribute()
  {
    return $this->parent_transaction->billing_address;
  }

  public function getShippingAddressAttribute()
  {
    return $this->parent_transaction->shipping_address;
  }

  public function getFirstNameAttribute()
  {
    return $this->parent_transaction->first_name;
  }

  public function getLastNameAttribute()
  {
    return $this->parent_transaction->last_name;
  }

  public function getEmailAttribute()
  {
    return $this->parent_transaction->email;
  }

  public function getPhoneNumberAttribute()
  {
    return $this->parent_transaction->phone_number;
  }

  public function getItemCountAttribute()
  {
    $count = 0;
    $transaction_line_items = $this->parent_transaction->transaction_line_items;

    foreach($transaction_line_items as $transaction_line_item)
      $count += $transaction_line_item->quantity;

    return $count;
  }

  public function getSelectedShippingMethodIdAttribute()
  {
    return $this->parent_transaction->selected_shipping_method_id;
  }

  /**
   * Return data prepared for checkout screen
   * @param ICartService $cartService
   * @return array
   */
  public function getDataForCheckout(ICartService $cartService)
  {
    return [
      'billing_address' => $this->billing_address,
      'shipping_address' => $this->shipping_address,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'sub_total' => $this->sub_total,
      'grand_total' => $this->total,
      'discount_amount' => $this->discount_amount,
      'email' => $this->email,
      'selected_shipping_method_id' => $this->selected_shipping_method_id,
      'shipping_total' => $this->shipping_total,
      'tax' => $this->tax,
      'gift_card_number' => $this->parent_transaction->gift_card_number,
      'gift_card_amount' => $this->parent_transaction->gift_card_amount,
      'list_items' => $this->line_items,
      'item_count' => $this->item_count,
      'shipping_methods' => $cartService->getShippingMethodData($this->parent_transaction)->toArray()
    ];
  }
}
