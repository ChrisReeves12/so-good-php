<?php

namespace App;
use App\Services\Contracts\ICartService;

/**
 * App\Transaction
 *
 * @property int $id
 * @property int $entity_id
 * @property float $discount_amount
 * @property int $discount_id
 * @property string $ip_address
 * @property string $full_name
 * @property int $billing_address_id
 * @property int $shipping_address_id
 * @property float $tax
 * @property float $total
 * @property float $sub_total
 * @property float $shipping_total
 * @property float $tax_rate
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone_number
 * @property string $transactionable_type
 * @property int $selected_shipping_method_id
 * @property int $transactionable_id
 * @property string $gift_card_number
 * @property float $gift_card_amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Address $billing_address
 * @property-read \App\Entity $entity
 * @property-read Address $ship_to_address
 * @property-read mixed $user
 * @property-read \App\Address $shipping_address
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TransactionLineItem[] $transaction_line_items
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereBillingAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereDiscountAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereDiscountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereEntityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction wherePhoneNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereShippingAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereShippingTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereSubTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereTax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereTaxRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereTransactionableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereTransactionableType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Transaction extends AbstractRecordType
{
  protected $guarded = [];

  public function getViewParameters(): array
  {
    return ['id', 'discount_amount', 'ip_address',
      'billing_address', 'shipping_address', 'tax', 'total', 'sub_total', 'user', 'gift_card_amount', 'gift_card_number',
      'shipping_total', 'tax_rate', 'first_name', 'transaction_line_items', 'last_name', 'email', 'phone_number'];
  }

  public function getSelectedShippingMethodIdAttribute()
  {
    $selected_shipping_method_id = null;
    $line_item = $this->transaction_line_items->first(function(TransactionLineItem $tli) {
      return(!empty($tli->shipping_method_id && ShippingMethod::whereRaw('is_inactive != ? AND id = ?', [true, $tli->shipping_method_id])->count() > 0));
    });

    if($line_item instanceof TransactionLineItem)
    {
      $selected_shipping_method_id = $line_item->shipping_method_id;
    }

    return $selected_shipping_method_id;
  }

  public function getUserAttribute()
  {
    return $this->entity;
  }

  public function entity()
  {
    return $this->belongsTo('App\Entity');
  }

  public function transaction_line_items()
  {
    return $this->hasMany('App\TransactionLineItem');
  }

  public function getFullNameAttribute()
  {
    return $this->first_name . ' ' . $this->last_name;
  }

  public function billing_address()
  {
    return $this->belongsTo('App\Address', 'billing_address_id', 'id');
  }

  public function shipping_address()
  {
    return $this->belongsTo('App\Address', 'shipping_address_id', 'id');
  }

  public function getShipToAddressAttribute()
  {
    return ($this->shipping_address instanceof Address) ? $this->shipping_address : $this->billing_address;
  }

  public function calculateTotals($force_recalculate_tax_rate = false, $save_line_items = false)
  {
    $this->sub_total = $this->total = $this->tax = $shipping_surcharges = $shipping_cost = $this->discount_amount = 0;

    /** @var TransactionLineItem $transaction_line_item */
    foreach($this->transaction_line_items as $transaction_line_item)
    {
      $transaction_line_item->calculateTotals($force_recalculate_tax_rate);

      if($save_line_items)
        $transaction_line_item->save();

      $this->sub_total += $transaction_line_item->sub_total;
      $this->total += $transaction_line_item->total_price;
      $this->tax += $transaction_line_item->tax;
      $this->discount_amount += $transaction_line_item->discount_amount;

      $shipping_surcharges += $transaction_line_item->shipping_charge;
    }

    // Check for free shipping
    /** @var ICartService $cart_service */
    $cart_service = app()->make(ICartService::class);
    $methods = $cart_service->getShippingMethodData($this);
    if($this->transactionable_type == 'ShoppingCart' && $methods->isNotEmpty() && !empty($this->selected_shipping_method_id))
    {
      $method_datum = $methods->first(function($element) {
        return($this->selected_shipping_method_id == $element['id']);
      });

      if(!empty($method_datum))
      {
        $this->shipping_total = $method_datum['price'];
      }
    }

    $this->total += ($shipping_surcharges + $this->shipping_total);
    if($this->gift_card_amount > 0)
    {
      $this->total = $this->total - $this->gift_card_amount;
    }

    // Don't let total be less than 0
    if($this->total < 0)
      $this->total = 0;

    // Round total to nearest tenth
    $this->total = number_format($this->total, 2, '.', '');

    return $this->total;
  }

  public function getValidationRules($data = []): array
  {
    return [
      'email' => 'required|email',
      'first_name' => 'required',
      'last_name' => 'required'
    ];
  }
}
