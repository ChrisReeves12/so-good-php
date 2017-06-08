<?php

namespace App;

use App\Contracts\ISolrDocumentable;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;

/**
 * App\Address
 *
 * @property int $id
 * @property string $payment_method
 * @property string $auth_code
 * @property float $payment_fees
 * @property float $sub_total
 * @property float $total
 * @property string $order_time
 * @property string $email
 * @property string $order_source
 * @property string $memo
 * @property string $payment_info
 * @property bool $is_fraud_detected
 * @property int $transaction_id
 * @property string $status
 * @property string $formatted_marketing_channel
 * @property array $reserved_inventory
 * @property bool $shipping_calc_needed
 * @property string $gift_card_number
 * @property float $gift_card_amount
 * @property array $tracking_numbers
 * @property array $discount_codes
 * @property string $marketing_channel
 * @property string $formatted_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Address $ship_to_address
 * @property Address $billing_address
 * @property Transaction $parent_transaction
 * @property \Illuminate\Support\Collection $line_items
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereId($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder wherePaymentMethod($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereAuthCode($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder wherePaymentFees($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereOrderSource($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder wherePaymentInfo($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereIsFraudDetected($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereTransactionId($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereStatus($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereShippingCalcNeeded($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereMarketingChannel($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereCreatedAt($value)
 * @method \Illuminate\Database\Query\Builder|\App\SalesOrder whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class SalesOrder extends AbstractRecordType implements ISolrDocumentable
{
  protected $guarded = [];
  protected $casts = ['tracking_numbers' => 'array', 'discount_codes' => 'array'];

  public function getViewParameters(): array
  {
    return ['id', 'payment_method', 'auth_code',
      'payment_fees', 'order_source', 'memo', 'payment_info',
      'is_fraud_detected', 'transaction_id', 'status', 'reserved_inventory', 'discount_codes',
      'tracking_numbers', 'marketing_channel', 'tracking_numbers', 'order_time', 'parent_transaction'];
  }

  public function getFormattedMarketingChannelAttribute()
  {
    return ucfirst($this->marketing_channel);
  }

  public function getLineItemsAttribute()
  {
    return $this->parent_transaction->transaction_line_items;
  }

  public function getGiftCardNumberAttribute()
  {
    return $this->parent_transaction->gift_card_number;
  }

  public function getGiftCardAmountAttribute()
  {
    return $this->parent_transaction->gift_card_amount;
  }

  public function getValidationRules($data = []): array
  {
    return [
      'status' => 'required|in:pending,processing,shipped,canceled',
      'payment_method' => 'valid_payment_method:' . ($this->total ?? 0) . ',' . $this->payment_method
    ];
  }

  public function getShipToAddressAttribute()
  {
    return $this->parent_transaction->ship_to_address;
  }

  public function getCustomerNameAttribute()
  {
    return $this->parent_transaction->entity->full_name ?? $this->parent_transaction->first_name . ' ' . $this->parent_transaction->last_name;
  }

  public function getBillingAddressAttribute()
  {
    return $this->parent_transaction->billing_address;
  }

  public function getFormattedStatusAttribute()
  {
    return ucfirst($this->status);
  }

  public function getShippingMethodLabelAttribute()
  {
    return !empty($this->parent_transaction->transaction_line_items->first()->shipping_method) ?
      $this->parent_transaction->transaction_line_items->first()->shipping_method->name : '';
  }

  public function getSubTotalAttribute()
  {
    return number_format($this->parent_transaction->sub_total, 2, '.', '');
  }

  public function getTotalAttribute()
  {
    return number_format($this->parent_transaction->total, 2, '.', '');
  }

  public function parent_transaction()
  {
    return $this->belongsTo('App\Transaction', 'transaction_id', 'id');
  }

  public function getFormattedMarketingChannel()
  {
    return ucfirst($this->marketing_channel);
  }

  public function getOrderTimeAttribute()
  {
    return human_time($this->created_at);
  }

  public function setTotalAttribute($value)
  {
      $this->parent_transaction->attributes['total'] = $value;
  }

    public function setSubTotalAttribute($value)
    {
        $this->parent_transaction->attributes['sub_total'] = $value;
    }

  public function getEmailAttribute()
  {
      return $this->parent_transaction->email;
  }

  /**
   * Overrides the delete function
   */
  public function delete()
  {
    // Delete all transaction list items
    TransactionLineItem::where('transaction_id', $this->transaction_id)->delete();

    // Delete the parent transaction
    Transaction::where('id', $this->transaction_id)->delete();
    SalesOrder::where('id', $this->id)->delete();
  }

  /**
   * Convert order to solr document
   * @param DocumentInterface $doc
   * @return DocumentInterface
   */
  public function toSolrDocument(DocumentInterface $doc): DocumentInterface
  {
    $doc->order_id = $this->id;
    $doc->first_name = $this->parent_transaction->first_name;
    $doc->last_name = $this->parent_transaction->last_name;
    $doc->email = $this->email;
    $doc->tracking_numbers = $this->tracking_numbers;
    $doc->total = $this->total;
    $doc->payment_method = $this->payment_method;
    $doc->sub_total = $this->sub_total;
    $doc->status = $this->status;
    $doc->address_line_1 = $this->parent_transaction->billing_address->line_1;
    $doc->address_line_2 = $this->parent_transaction->billing_address->line_2;
    $doc->city = $this->parent_transaction->billing_address->city;
    $doc->state = $this->parent_transaction->billing_address->state;
    $doc->zip_code = $this->parent_transaction->billing_address->zip;
    $doc->phone_number = $this->parent_transaction->phone_number;

    return $doc;
  }
}
