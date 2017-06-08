<?php

namespace App;

use Illuminate\Support\Facades\Log;

/**
 * App\TransactionLineItem
 *
 * @property int $id
 * @property int $item_id
 * @property int $transaction_line_itemable_id
 * @property string $transaction_line_itemable_type
 * @property float $unit_price
 * @property float $total_price
 * @property float $discount_amount
 * @property float $tax_rate
 * @property float $tax
 * @property float $shipping_charge
 * @property float $sub_total
 * @property string $name
 * @property int $quantity
 * @property string $status
 * @property int $transaction_id
 * @property int $ship_from_location_id
 * @property int $shipping_method_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $details_for_checkout
 * @property-read mixed $image_url
 * @property-read mixed $item_url
 * @property-read \App\Item $item
 * @property-read \App\StockLocation $ship_from_location
 * @property-read \App\ShippingMethod $shipping_method
 * @property-read \App\Transaction $transaction
 * @property-read \App\Transaction $parent_transaction
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereShipFromLocationId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereShippingCharge($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereShippingMethodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereSubTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereTax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereTaxRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereTransactionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereTransactionLineItemableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereTransactionLineItemableType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TransactionLineItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TransactionLineItem extends AbstractRecordType
{
  protected $guarded = [];
  protected $appends = ['image_url', 'item_url', 'details_for_checkout'];

  public function item()
  {
    return $this->belongsTo('App\Item');
  }

  public function getDetailsForCheckoutAttribute()
  {
    $output = '';
    if(!empty($this->item->details))
    {
      $output = '<ul>';
      foreach($this->item->details as $detail)
      {
        $output .= '<li>' . $detail['key'] . ': ' . $detail['value'] . '</li>';
      }

      $output .= '</ul>';
    }

    return $output;
  }

  public function getItemUrlAttribute()
  {
    return $this->item->url;
  }

  public function transaction()
  {
    return $this->belongsTo('App\Transaction');
  }

  public function getParentTransactionAttribute()
  {
    return $this->transaction;
  }

  public function getImageUrlAttribute()
  {
    if(!empty($this->item->image) || !empty($this->item->product->default_image))
    {
      $image = empty($this->item->image) ? $this->item->product->getImageUrl($this->item->product->default_image) :
        $this->item->getImageUrl($this->item->image);
    }

    return $image ?? '';
  }

  public function shipping_method()
  {
    return $this->belongsTo('App\ShippingMethod');
  }

  public function getViewParameters(): array
  {
    return ['id', 'item_id', 'item', 'status', 'shipping_method_id', 'unit_price', 'total_price', 'shipping_method', 'image_url',
      'quantity', 'discount_amount', 'tax', 'shipping_charge', 'sub_total', 'name', 'transaction_id', 'ship_from_location',
      'ship_from_location_id'];
  }

  public function ship_from_location()
  {
    return $this->belongsTo('App\StockLocation');
  }

  /**
   * Calculate line item total
   * @param bool $force_fetch_tax_rate
   * @return float
   */
  public function calculateTotals($force_fetch_tax_rate = false)
  {
    $this->sub_total = $this->total_price = $this->tax = 0;
    $this->sub_total = number_format($this->unit_price * $this->quantity, 2, '.', '');

    // Calculate discount, find corresponding cart
    if($this->transaction_line_itemable_type == 'ShoppingCart')
    {
      $shopping_cart = ShoppingCart::whereRaw('transaction_id = ?', $this->transaction_id)->first();
      $discount_codes = $shopping_cart->discount_codes;
      $this->discount_amount = 0;

      if(!empty($discount_codes))
      {
        if(in_array('YARA10', $discount_codes)) // yara 10% off - one time only
        {
            $sales_order_with_yara_discount = SalesOrder::whereRaw('discount_codes @> ?', ['["YARA10"]'])->get();
            $customer_email = $this->parent_transaction->email;

            $filtered_sale_orders = $sales_order_with_yara_discount->filter(function(SalesOrder $order) use($customer_email) {
                return strtolower($order->email) == strtolower($customer_email);
            });

            $eligible = ($filtered_sale_orders->isEmpty());

            if($eligible)
            {
                $this->discount_amount = number_format(($this->sub_total * 0.10), 2, '.', '') * -1;
            }
        }
        elseif(in_array('CURLS10', $discount_codes)) // 10% off for instagram- one time only
        {
            $sales_order_with_insta_discount = SalesOrder::whereRaw('discount_codes @> ?', ['["CURLS10"]'])->get();
            $customer_email = $this->parent_transaction->email;

            $filtered_sale_orders = $sales_order_with_insta_discount->filter(function(SalesOrder $order) use($customer_email) {
                return strtolower($order->email) == strtolower($customer_email);
            });

            $eligible = ($filtered_sale_orders->isEmpty());

            if($eligible)
            {
                $this->discount_amount = number_format(($this->sub_total * 0.10), 2, '.', '') * -1;
            }
        }
        elseif(in_array('YARA5OFF', $discount_codes)) // $5 off for video reviewers - one time only
        {
            $sales_order_with_yara_video_discount = SalesOrder::whereRaw('discount_codes @> ?', ['["YARA5OFF"]'])->get();
            $customer_email = $this->parent_transaction->email;

            $filtered_sale_orders = $sales_order_with_yara_video_discount->filter(function(SalesOrder $order) use($customer_email) {
                return strtolower($order->email) == strtolower($customer_email);
            });

            $eligible = ($filtered_sale_orders->isEmpty());

            if($eligible)
            {
                $this->discount_amount = number_format(5,2,'.', '') * -1;
            }
        }
        elseif(in_array('SAVE5', $discount_codes)) // $5 off for video reviewers - one time only
        {
            $sales_order_with_subscribe_discount = SalesOrder::whereRaw('discount_codes @> ?', ['["SAVE5"]'])->get();
            $customer_email = $this->parent_transaction->email;

            $filtered_sale_orders = $sales_order_with_subscribe_discount->filter(function(SalesOrder $order) use($customer_email) {
                return strtolower($order->email) == strtolower($customer_email);
            });

            $eligible = ($filtered_sale_orders->isEmpty());

            if($eligible)
            {
                $this->discount_amount = number_format(($this->sub_total * 0.05), 2, '.', '') * -1;
            }
        }
      }
    }

    // Calculate tax
    try
    {
      if($this->transaction->ship_to_address instanceof Address)
      {
        if(in_array($this->transaction->ship_to_address->fresh()->state, business('tax_states')))
        {
          // Connect to tax api to get tax rate
          if($force_fetch_tax_rate || empty($this->tax_rate))
          {
            $address = $this->transaction->ship_to_address;
            $tax_api_endpoint = strtolower('https://taxrates.api.avalara.com/address?country=usa&state=' . urlencode($address->state) . '&city=' . urlencode($address->city) . '&postal=' . urlencode($address->zip) . '&street=' . urlencode($address->line_1) . '&apikey=') . urlencode(business('avalara_api_key'));
            $curl = curl_init($tax_api_endpoint);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
            $response = curl_exec($curl);

            if(!$response)
            {
              throw new \Exception('Did not get a response back from Avalara.');
            }

            $response_json = json_decode($response, true);
            $rate = $response_json['totalRate'] ?? null;
            if(empty($rate))
            {
              throw new \Exception('Did not get tax rate back from Avalara.');
            }

            $this->update(['tax_rate' => $rate]);
          }

          // Calculate price
          $this->tax_rate = $this->tax_rate ?? 0;
          $this->tax = number_format((($this->tax_rate * 0.01) * $this->sub_total), 2, '.', '');
        }
        else
        {
          if($this->tax != 0 || $this->tax_rate != null)
          {
            $this->update(['tax_rate' => null, 'tax' => 0]);
            $this->tax = 0;
            $this->tax_rate = null;
          }
        }
      }
    }
    catch(\Exception $ex)
    {
      Log::error('TransactionLineItems::calculateTotals (Tax Calculation): ' . $ex->getMessage());
    }

    $this->total_price = number_format($this->sub_total + ($this->discount_amount ?? 0) + ($this->tax ?? 0) + ($this->shipping_charge ?? 0), 2, '.', '');
    return $this->total_price;
  }
}
