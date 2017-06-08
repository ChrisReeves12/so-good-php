<?php
/**
 * The SalesOrderServiceImpl class definition.
 *
 * Sales Order service.
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Address;
use App\NoSQLDataSourceResult;
use App\Services\Contracts\IAdminSearchable;
use App\Services\Contracts\ICRUDRecordTypeService;
use App\Services\Contracts\INoSQLDataSourceService;
use App\Services\Contracts\ISalesOrderService;
use App\Item;
use App\Mail\TrackingNumbersMessage;
use App\RelStockLocationItem;
use App\SalesOrder;
use App\ShoppingCart;
use App\Transaction;
use App\TransactionLineItem;
use Mail;

/**
 * Class SalesOrderServiceImpl
 * @package App\Services
 */
class SalesOrderServiceImpl implements ISalesOrderService, ICRUDRecordTypeService, IAdminSearchable
{
  protected $noSQLDataSource;

  /**
   * SalesOrderServiceImpl constructor.
   * @param INoSQLDataSourceService $noSQLDataSource
   */
  public function __construct(INoSQLDataSourceService $noSQLDataSource)
  {
    $this->noSQLDataSource = $noSQLDataSource;
  }

  /**
   * Create or update sales order
   * @param $sales_order
   * @param array $data
   * @return array
   */
  public function createUpdate($sales_order, array $data = [])
  {
    $ret_val = ['system_error' => false, 'errors' => false];
    $send_tracking_number_email = false;

    try
    {
      // Validate billing address
      $billing_address_errors = $shipping_address_errors = [];
      $is_billing_address_valid = with(new Address)->validate($data['data']['parent_transaction']['billing_address'], $billing_address_errors);

      // Validate shipping address, only if there is a field set
      $validate_shipping_address = false;
      $add_shipping_address = false;
      foreach($data['data']['parent_transaction']['shipping_address'] as $value)
      {
        if(!empty($value))
        {
          $add_shipping_address = true;
          $validate_shipping_address = true;
          break;
        }
      }

      $is_shipping_address_valid = true;
      if($validate_shipping_address)
      {
        $is_shipping_address_valid = with(new Address)->validate($data['data']['parent_transaction']['shipping_address'], $shipping_address_errors);
      }

      // Validate the sales order
      $sales_order_errors = [];
      $is_sales_order_valid = $sales_order->validate($data['data'], $sales_order_errors);

      // Validate transaction
      $transaction_errors = [];
      $is_transaction_valid = with(new Transaction)->validate($data['data']['parent_transaction'], $transaction_errors);

      // Compile all validation errors
      if(!$is_billing_address_valid || !$is_shipping_address_valid || !$is_sales_order_valid || !$is_transaction_valid)
      {
        // Billing address errors
        $ret_val['errors'] = [];
        if(!$is_billing_address_valid)
        {
          foreach($billing_address_errors as $billing_address_error)
          {
            $ret_val['errors']['billing_address.' . key($billing_address_error)] = array_values($billing_address_error);
          }
        }

        // Shipping address errors
        if(!$is_shipping_address_valid)
        {
          foreach($shipping_address_errors as $shipping_address_error)
          {
            $ret_val['errors']['shipping_address.' . key($billing_address_error)] = array_values($shipping_address_error);
          }
        }

        // Transaction errors
        if(!$is_transaction_valid)
        {
          foreach($transaction_errors as $transaction_error)
          {
            $ret_val['errors']['parent_transaction.' . key($transaction_error)] = array_values($transaction_error);
          }
        }

        // Sales order errors
        if(!$is_sales_order_valid)
        {
          foreach($sales_order_errors as $sales_order_error)
          {
            $ret_val['errors'][key($sales_order_error)] = array_values($sales_order_error);
          }
        }
      }
      else // The order is valid
      {
        // Save addresses and transaction first
        $transaction_data = $data['data']['parent_transaction'];
        $transaction_data['transactionable_type'] = 'SalesOrder';
        $transaction_line_data = $data['data']['parent_transaction']['transaction_line_items'];
        $billing_address_data = $data['data']['parent_transaction']['billing_address'];
        $shipping_address_data = $data['data']['parent_transaction']['shipping_address'];

        foreach($transaction_line_data as &$line_datum)
          unset($line_datum['image_url']);

        unset($transaction_data['billing_address']);
        unset($transaction_data['shipping_address']);
        unset($transaction_data['transaction_line_items']);
        unset($transaction_data['user']);

        if(empty($sales_order->parent_transaction->billing_address))
        {
          $billing_address = Address::create($billing_address_data);
        }
        else
        {
          $sales_order->parent_transaction->billing_address()->update($billing_address_data);
          $billing_address = $sales_order->parent_transaction->billing_address;
        }

        $transaction_data['billing_address_id'] = $billing_address->id;

        if($add_shipping_address)
        {
          if(empty($sales_order->parent_transaction->shipping_address))
          {
            $shipping_address = Address::create($shipping_address_data);
          }
          else
          {
            $sales_order->parent_transaction->shipping_address()->update($shipping_address_data);
            $shipping_address = $sales_order->parent_transaction->shipping_address;
          }

          $transaction_data['shipping_address_id'] = $shipping_address->id;
        }
        else
        {
          $transaction_data['shipping_address_id'] = null;
        }

        if(empty($sales_order->parent_transaction))
        {
          $transaction = Transaction::create($transaction_data);
        }
        else
        {
          $sales_order->parent_transaction()->update($transaction_data);
          $transaction = $sales_order->parent_transaction;
        }

        // Save sales order line items
        if($sales_order->exists)
          TransactionLineItem::where('transaction_id', $transaction->id)->delete();

        foreach($transaction_line_data as $line_item_data)
        {
          $line_item_data['transaction_id'] = $transaction->id;
          $line_item_data['transaction_line_itemable_id'] = $transaction->id;
          $line_item_data['transaction_line_itemable_type'] = 'SalesOrder';
          $line_item_data['item_id'] = $line_item_data['item']['id'];
          $line_item_data['name'] = Item::find($line_item_data['item_id'])->sku;

          unset($line_item_data['id']);
          unset($line_item_data['saved']);
          unset($line_item_data['item']);
          unset($line_item_data['ship_from_location']);
          unset($line_item_data['shipping_method']);
          TransactionLineItem::create($line_item_data);
        }

        // Total up orders
        $transaction->calculateTotals();

        /** @var TransactionLineItem $transaction_line_item */
        foreach($transaction->transaction_line_items as $transaction_line_item)
          $transaction_line_item->save();

        $transaction->save();

        // Save sales order
        $sales_order_data = $data['data'];
        $sales_order_data['transaction_id'] = $transaction->id;
        $sales_order_data['payment_fees'] = $sales_order_data['payment_fees'] ?? 0;
        $sales_order_data['order_source'] = 'admin';
        $sales_order_data['tracking_numbers'] = empty($sales_order_data['tracking_numbers_data']) ? null : json_encode($sales_order_data['tracking_numbers_data']);
        unset($sales_order_data['parent_transaction']);
        unset($sales_order_data['tracking_numbers_data']);
        unset($sales_order_data['order_time']);

        // Update inventories if it is being changed to shipped
        if($sales_order->exists)
        {
          if(in_array($sales_order->status, ['pending', 'processing']))
          {
            if($sales_order_data['status'] == 'shipped')
            {
              foreach($transaction_line_data as $line_item_data)
              {
                $rsl = RelStockLocationItem::whereRaw('item_id = ? AND stock_location_id = ?', [$line_item_data['item_id'], $line_item_data['ship_from_location_id']])->first();
                $rsl->quantity_reserved = $rsl->quantity_reserved - $line_item_data['quantity'];
                $rsl->save();
              }

              $send_tracking_number_email = true;
            }
          }
          elseif(in_array($sales_order->status, ['shipped']))
          {
            // Cannot change from shipped
            $sales_order_data['status'] = $sales_order->status;
          }
        }
        else
        {
          $sales_order_data['status'] = 'pending';
        }

        if(!empty($sales_order_data['lines_for_deletion']))
        {
          foreach($sales_order_data['lines_for_deletion'] as $delete_line)
          {
            TransactionLineItem::where('id', $delete_line)->delete();
          }

          unset($sales_order_data['lines_for_deletion']);
        }

        if($sales_order->exists)
          $sales_order->update($sales_order_data);
        else
        {
          $sales_order->fill($sales_order_data);
          $sales_order->save();
        }

        $ret_val['id'] = $sales_order->id;
      }
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    // Send email
    if($send_tracking_number_email)
      Mail::to($sales_order->parent_transaction->email)->send(new TrackingNumbersMessage($sales_order));

    return $ret_val;
  }

  /**
   * Transform cart into sales order
   * @param ShoppingCart $cart
   * @param string $payment_method
   * @param string $marketing_source
   * @return SalesOrder
   */
  public function transformToSalesOrder(ShoppingCart $cart, $payment_method, $marketing_source = 'N/A')
  {
    $sales_order = new SalesOrder();
    $sales_order->marketing_channel = $marketing_source;
    $sales_order->parent_transaction()->associate($cart->parent_transaction);
    $sales_order->status = 'pending';
    $sales_order->discount_codes = $cart->discount_codes;
    $sales_order->payment_method = $payment_method;
    $sales_order->order_source = 'website';
    $cart->parent_transaction->update(['transactionable_type' => 'SalesOrder']);
    $cart->parent_transaction->transaction_line_items()->update(['transaction_line_itemable_type' => 'SalesOrder']);
    $transaction_line_items = $cart->parent_transaction->transaction_line_items;

    // Find ideal stock location
    /** @var TransactionLineItem $transaction_line_item */
    foreach($transaction_line_items as $transaction_line_item)
    {
      $ideal_stock_location = $transaction_line_item->item->getIdealStockLocation($transaction_line_item->quantity);
      $transaction_line_item->ship_from_location()->associate($ideal_stock_location);
      $transaction_line_item->save();
    }

    $sales_order->save();

    return $sales_order;
  }

  /**
   * Find a sales order by id
   * @param int $id
   * @return mixed
   */
  public function findById(int $id)
  {
    return SalesOrder::find($id);
  }

  /**
   * Validator to check if the payment method is valid
   * @param string $attribute
   * @param array $value
   * @param array $parameters
   * @return bool
   */
  public function validatePaymentMethod($attribute, $value, $parameters)
  {
    $is_valid = true;

    // If the total is 0, no payment method is needed
    if(!isset($parameters[0]) || $parameters[0] > 0)
    {
      $payment_method = $parameters[1] ?? 'none';
      $is_valid = in_array($payment_method, ['paypal', 'credit_card']);
    }

    return $is_valid;
  }

  /**
   * Update sales order search
   */
  public function updateSalesOrderIndex()
  {
    $result = $this->noSQLDataSource->updateCollectionIndex('sales_orders', SalesOrder::class);
    if(!$result['success'])
    {
      throw $result['error'];
    }
  }

  /**
   * Returns results for admin search
   * @param string $keyword
   * @return array
   */
  public function handleAdminSearch(string $keyword): array
  {
    $ret_val = [];
    $results = $this->noSQLDataSource->findBy('sales_orders', ['*all*' => $keyword], ['max_results' => 10]);

    if($results->getResults()->count() > 0)
    {
      /** @var NoSQLDataSourceResult $result */
      foreach($results->getResults() as $result)
      {
        $ret_val[] = [
          'id' => $result->get('order_id'),
          'name' => $result->get('first_name') . ' ' . $result->get('last_name'),
          'link' => '/admin/sales-order/' . $result->get('order_id'),
          'extra_info' => [
            'Email' => $result->get('email'),
            'Status' => ucfirst($result->get('status')),
            'Sub Total' => money($result->get('sub_total')),
            'Total' => money($result->get('total')),
            'Payment Method' => $result->get('payment_method')
          ]
        ];
      }
    }

    return $ret_val;
  }
}