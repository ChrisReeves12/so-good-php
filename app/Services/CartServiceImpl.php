<?php
/**
 * The CartServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Address;
use App\GiftCard;
use App\Services\Contracts\ICartService;
use App\Services\Contracts\ISalesOrderService;
use App\Entity;
use App\Item;
use App\Mail\SalesOrderReceiptMessage;
use App\Product;
use App\SalesOrder;
use App\ShippingMethod;
use App\ShoppingCart;
use App\Transaction;
use App\TransactionLineItem;
use DB;
use Illuminate\Support\Collection;
use Mail;
use Validator;
use Stripe\Charge;
use Stripe\Stripe;

/**
 * Class CartServiceImpl
 * @package App\Services
 */
class CartServiceImpl implements ICartService
{
  protected $salesOrderService;
  protected $shopping_cart;

  /**
   * CartServiceImpl constructor.
   * @param ISalesOrderService $salesOrderService
   */
  public function __construct(ISalesOrderService $salesOrderService)
  {
    $this->salesOrderService = $salesOrderService;
  }

  /**
   * Do payment checkout
   * @param bool $test_order
   * @param array $paypal_data
   * @param array $card_data
   * @param string $marketing_channel
   * @return array
   * @throws \Exception
   */
  public function doCheckout(bool $test_order = false, array $paypal_data = [], array $card_data = [], string $marketing_channel): array
  {
    $shopping_cart = $this->getCurrentCart();
    $ret_val = ['card_error' => false, 'errors' => false, 'id' => null];

    try
    {
      if(!($shopping_cart instanceof ShoppingCart))
      {
        throw new \Exception('Shopping cart cannot be found during checkout.');
      }

      DB::beginTransaction();

      $pay_method = 'none';
      if(!empty($paypal_data))
      {
        $pay_method = 'paypal';
      }
      elseif(!empty($card_data))
      {
        $pay_method = 'credit_card';
      }

      // No payment necessary if there is no payment method
      $payment_was_successful = ($pay_method == 'none');

      /** @var SalesOrder $sales_order */
      $sales_order = $this->salesOrderService->transformToSalesOrder($shopping_cart, $pay_method, $marketing_channel);

      // PayPal payments get approved before Sales Order gets created
      if($pay_method == 'paypal')
      {
        // Paypal already authorized if we got to this point
        $payment_was_successful = true;
      }
      elseif($pay_method == 'credit_card')
      {
        // Charge card
        $ip = $card_data['client_ip'];
        $shopping_cart->parent_transaction->calculateTotals();

        $stripe_api_key = ($test_order) ? business('dev_stripe_api_key') : business('stripe_api_key');
        Stripe::setApiKey($stripe_api_key);
        $charge = Charge::create([
          'amount'      => intval($shopping_cart->total * 100),
          'currency'    => 'usd',
          'source'      => $card_data['id'],
          'description' => 'Order for cart: ' . $shopping_cart->id
        ]);

        if($charge->failure_code)
        {
          // Card problem
          DB::rollback();
          $ret_val['card_error'] = $charge->failure_message;
        }
        else
        {
          $payment_was_successful = true;
        }
      }

      // Update order with payment information
      if($payment_was_successful)
      {
        // Deduct gift card if applicable
        if(is_numeric($shopping_cart->parent_transaction->gift_card_amount) && $shopping_cart->parent_transaction->gift_card_amount > 0)
        {
          if(!empty($shopping_cart->parent_transaction->gift_card_number))
          {
            $gift_card = GiftCard::where('number', $shopping_cart->parent_transaction->gift_card_number)->where('is_inactive', false)->first();
            if($gift_card instanceof GiftCard)
            {
              $new_balance = ($gift_card->balance < $shopping_cart->parent_transaction->gift_card_amount) ?
                0 : ($gift_card->balance - $shopping_cart->parent_transaction->gift_card_amount);

              if($new_balance < 0)
                $new_balance = 0;

              $gift_card->balance = number_format($new_balance, 2, '.', '');
              $gift_card->save();
            }
          }
        }

        DB::commit();
        $ret_val['id'] = $sales_order->id;

        // Save payment information
        DB::beginTransaction();

        if($pay_method == 'credit_card' && !empty($charge))
        {
          $sales_order->parent_transaction->ip_address = $ip ?? '0.0.0.0';
          $sales_order->payment_info = 'Credit Card Last 4: ' . $card_data['card']['last4'];
          $sales_order->auth_code = $charge->id;
        }
        elseif($pay_method == 'paypal')
        {
          $sales_order->payment_info = 'Payer ID: ' . $paypal_data['payer']['payer_info']['payer_id'] . ' | Status: ' . $paypal_data['state'];
          $sales_order->auth_code = $paypal_data['id'];
        }

        $sales_order->save();
        $ret_val['id'] = $sales_order->id;

        $shopping_cart->delete();
        DB::commit();

        Mail::to($sales_order->parent_transaction->email)->send(new SalesOrderReceiptMessage($sales_order));
      }
    }
    catch(\Exception $ex)
    {
      DB::rollback();
      throw $ex;
    }

    return $ret_val;
  }

  /**
   * Add item to shopping cart
   * @param Entity $current_user_id
   * @param array $option_values
   * @param int $product_id
   * @param int $quantity
   * @return TransactionLineItem
   * @throws \Exception
   */
  public function add($current_user_id, $option_values, $product_id, $quantity = 1)
  {
    try
    {
      $shopping_cart = $this->getCurrentCart();
      $current_user = null;
      if(!empty($current_user_id))
      {
        $current_user = Entity::find($current_user_id);
      }

      // Check for an existing cart in session
      if(empty($shopping_cart))
      {
        DB::beginTransaction();
        $shopping_cart = new ShoppingCart();

        // Add logged in user to cart
        if(!empty($current_user))
        {
          // Get addresses for transaction
          $billing_address = clone $current_user->billing_address;
          $shipping_address = !empty($current_user->shipping_address) ? clone $current_user->shipping_address : null;
          $billing_address->save();

          if(!empty($shipping_address))
          {
            $shipping_address->save();
          }
        }

        // Create a new transaction
        $transaction = new Transaction([
          'entity_id'            => !empty($current_user) ? $current_user->id : null,
          'first_name'           => !empty($current_user) ? $current_user->first_name : null,
          'last_name'            => !empty($current_user) ? $current_user->last_name : null,
          'email'                => !empty($current_user) ? $current_user->email : null,
          'phone_number'         => !empty($current_user) ? $current_user->phone_number : null,
          'transactionable_type' => 'ShoppingCart'
        ]);

        if(!empty($billing_address))
        {
          $transaction->billing_address()->associate($billing_address);
        }

        if(!empty($shipping_address))
        {
          $transaction->shipping_address()->associate($shipping_address);
        }

        $transaction->save();

        $shopping_cart->parent_transaction()->associate($transaction);
        $shopping_cart->save();

        $transaction->update(['transactionable_id' => $shopping_cart->id]);
        DB::commit();

        session(['cart_id' => $shopping_cart->id]);
      }

      // Add item to cart, check to see if the item is already in cart
      DB::beginTransaction();
      if(!empty($option_values))
      {
        $item = Item::findFromOptions($product_id, $option_values);
      }
      else // This is a single sku product
      {
        $item = Product::find($product_id)->default_item;
      }

      $transaction_line_item = $shopping_cart->parent_transaction->transaction_line_items()
        ->where('item_id', $item->id)
        ->first();

      // Update quantity if line exists
      if($transaction_line_item instanceof TransactionLineItem)
      {
        $transaction_line_item->quantity = $transaction_line_item->quantity + $quantity;

        // Ensure quantity does not exceed what can be ordered
        if($transaction_line_item->quantity > $transaction_line_item->item->single_orderable_quantity)
        {
          throw new \Exception('You cannot any more of this item to your cart due to insufficient stock.');
        }

        $transaction_line_item->calculateTotals();
        $transaction_line_item->save();
      }
      else
      {
        /** @var TransactionLineItem $transaction_line_item */
        $transaction_line_item = TransactionLineItem::create([
          'item_id'                        => $item->id,
          'quantity'                       => $quantity,
          'unit_price'                     => $item->store_price,
          'transaction_id'                 => $shopping_cart->parent_transaction->id,
          'transaction_line_itemable_type' => 'ShoppingCart',
          'transaction_line_itemable_id'   => $shopping_cart->id,
          'name'                           => $item->sku . ' - ' . $item->product->name,
          'status'                         => $item->calculated_stock_status
        ]);

        $transaction_line_item->calculateTotals();
        $transaction_line_item->save();
      }

      DB::commit();

      // Recalculate total of cart and save
      $shopping_cart->fresh();
      $shopping_cart->parent_transaction->calculateTotals();
      $shopping_cart->parent_transaction->save();
    }
    catch(\Exception $ex)
    {
      DB::rollback();
      throw $ex;
    }

    return $transaction_line_item;
  }

  /**
   * Deletes the line item
   * @param int $line_id
   */
  public function deleteLineItem($line_id)
  {
    $shopping_cart = $this->getCurrentCart();
    TransactionLineItem::where('id', $line_id)->delete();
    $shopping_cart->fresh();

    $shopping_cart->parent_transaction->calculateTotals();
    $shopping_cart->parent_transaction->save();
  }

  /**
   * Update line item quantity
   * @param $line_id
   * @param $quantity
   * @return TransactionLineItem
   */
  public function updateLineItemQuantity($line_id, $quantity): TransactionLineItem
  {
    /** @var TransactionLineItem $transaction_line_item */
    $shopping_cart = $this->getCurrentCart();
    $transaction_line_item = TransactionLineItem::where('id', $line_id)->firstOrFail();
    $transaction_line_item->quantity = $quantity;
    $transaction_line_item->calculateTotals();
    $transaction_line_item->save();
    $shopping_cart->fresh();

    $shopping_cart->parent_transaction->calculateTotals();
    $shopping_cart->parent_transaction->save();

    return $transaction_line_item;
  }

  /**
   * Update shipping method on cart
   * @param int $shipping_method_id
   */
  public function updateShippingMethod($shipping_method_id)
  {
    $shopping_cart = $this->getCurrentCart();
    $shopping_cart->parent_transaction->transaction_line_items()->update(['shipping_method_id' => $shipping_method_id]);
    $shopping_cart->parent_transaction->fresh();
    $shopping_cart->parent_transaction->calculateTotals();
    $shopping_cart->parent_transaction->save();
  }

  /**
   * Update addresses
   * @param array $billing_address_data
   * @param array $shipping_address_data
   * @param bool $same_as_billing
   * @return array
   */
  public function updateAddresses($billing_address_data, $shipping_address_data, $same_as_billing): array
  {
    $shopping_cart = $this->getCurrentCart();
    $ret_val = ['errors' => false];
    $same_as_billing = ($same_as_billing == 'true');

    // Validate billing address
    $validate_rules = (new Address())->getValidationRules();
    $validator = Validator::make($billing_address_data, $validate_rules);
    if($validator->fails())
    {
      $errors = $validator->errors()->toArray();
      $ret_val['errors'] = [];
      foreach($errors as $key => $error)
      {
        $ret_val['errors'][] = ['billing_address.' . $key => $error];
      }
    }

    // Validate shipping address
    if(!$same_as_billing)
    {
      $validator = Validator::make($shipping_address_data, $validate_rules);
      if($validator->fails())
      {
        $errors = $validator->errors()->toArray();

        if(empty($ret_val['errors']))
        {
          $ret_val['errors'] = [];
        }

        foreach($errors as $key => $error)
        {
          $ret_val['errors'][] = ['shipping_address.' . $key => $error];
        }
      }
    }

    // Save addresses
    if(empty($ret_val['errors']))
    {
      if($shopping_cart->parent_transaction->billing_address instanceof Address)
      {
        $shopping_cart->parent_transaction->billing_address()->update($billing_address_data);
      }
      else
      {
        $shopping_cart->parent_transaction->billing_address()->associate(Address::create($billing_address_data));
      }

      if(!$same_as_billing)
      {
        if($shopping_cart->parent_transaction->shipping_address instanceof Address)
        {
          $shopping_cart->parent_transaction->shipping_address()->update($shipping_address_data);
        }
        else
        {
          $shopping_cart->parent_transaction->shipping_address()->associate(Address::create($shipping_address_data));
        }
      }
      else
      {
        // Remove shipping address
        if(!empty($shopping_cart->parent_transaction->shipping_address))
        {
          $shopping_cart->parent_transaction->shipping_address()->delete();
          $shopping_cart->parent_transaction->shipping_address()->dissociate();
        }
      }

      // Recalculate everything
      $shopping_cart->parent_transaction->calculateTotals(true, true);
      $shopping_cart->parent_transaction->save();
    }

    return $ret_val;
  }

  /**
   * Validate the checkout information
   * @return array
   * @throws \Exception
   */
  public function validateCheckoutForm(): array
  {
    $shopping_cart = $this->getCurrentCart();
    $ret_val = ['errors' => false, 'missing_records' => false];

    if(!($shopping_cart instanceof ShoppingCart))
    {
      throw new \Exception('Cannot locate shopping cart.');
    }

    // Validate if billing address exists
    $address_validator_rules = (new Address())->getValidationRules();
    if($shopping_cart->parent_transaction->billing_address instanceof Address)
    {
      $validator = Validator::make($shopping_cart->parent_transaction->billing_address->toArray(), $address_validator_rules);
      if($validator->fails())
      {
        $errors = $validator->errors()->toArray();
        if(!is_array($ret_val['errors']))
        {
          $ret_val['errors'] = [];
        }

        foreach($errors as $key => $error)
        {
          $ret_val['errors'][] = ['billing_address' . $key => $error];
        }
      }
    }
    else
    {
      // Record error
      if(!is_array($ret_val['missing_records']))
      {
        $ret_val['missing_records'] = [];
      }

      $ret_val['missing_records'][] = 'Please provide a billing address, and make sure you click the "Save Address Changes" button.';
    }

    // Validate shipping address
    if($shopping_cart->parent_transaction->shipping_address instanceof Address)
    {
      $validator = Validator::make($shopping_cart->parent_transaction->shipping_address->toArray(), $address_validator_rules);
      if($validator->fails())
      {
        $errors = $validator->errors()->toArray();
        if(!is_array($ret_val['errors']))
        {
          $ret_val['errors'] = [];
        }

        foreach($errors as $key => $error)
        {
          $ret_val['errors'][] = ['shipping_address' . $key => $error];
        }
      }
    }

    // Validate there is at least one line item on the order
    if($shopping_cart->line_items->isEmpty())
    {
      if(!is_array($ret_val['missing_records']))
      {
        $ret_val['missing_records'] = [];
      }

      $ret_val['missing_records'][] = 'There must be at least one item in your shopping cart.';
    }
    else
    {
      // Validate all lines have a shipping method
      $line_items = $shopping_cart->line_items;
      $missing_shipping_method = false;

      /** @var TransactionLineItem $line_item */
      foreach($line_items as $line_item)
      {
        if(!($line_item->shipping_method instanceof ShippingMethod))
        {
          $missing_shipping_method = true;
        }
      }

      if($missing_shipping_method)
      {
        if(!is_array($ret_val['missing_records']))
        {
          $ret_val['missing_records'] = [];
        }

        $ret_val['missing_records'][] = 'Please select a shipping method.';
      }
    }

    // Validate email, name and other personal information is on the order
    $validator = Validator::make($shopping_cart->parent_transaction->toArray(), [
      'first_name' => 'required',
      'last_name'  => 'required',
      'email'      => 'required|email'
    ]);

    if($validator->fails())
    {
      $errors = $validator->errors()->toArray();
      if(!is_array($ret_val['errors']))
      {
        $ret_val['errors'] = [];
      }

      foreach($errors as $key => $error)
      {
        $ret_val['errors'][] = [$key => $error];
      }
    }

    return $ret_val;
  }

  /**
   * Updates user information on cart
   * @param array $user_data
   * @return array
   */
  public function updateUserInfo($user_data): array
  {
    $shopping_cart = $this->getCurrentCart();
    $ret_val = ['errors' => false];

    // Validate form
    $validator = Validator::make($user_data, [
      'first_name' => 'required|alpha_dash',
      'last_name'  => 'required|alpha_dash',
      'email'      => 'required|email'
    ]);

    if($validator->fails())
    {
      $ret_val['errors'] = [];
      $errors = $validator->errors()->toArray();
      foreach($errors as $key => $error)
      {
        $ret_val['errors'][] = [$key => $error];
      }
    }
    else
    {
      $shopping_cart->parent_transaction()->update($user_data);
    }

    return $ret_val;
  }

  /**
   * Update the discount code
   * @param string $discount_code
   * @param array $available_discount_codes
   * @return array
   * @throws \Exception
   */
  public function updateDiscountCode($discount_code, array $available_discount_codes): array
  {
    $shopping_cart = $this->getCurrentCart();
    $ret_val = ['system_error' => false];

    if(!in_array(strtoupper(trim($discount_code)), $available_discount_codes))
    {
      throw new \Exception('The discount code applied is not valid, please try again.');
    }

    $discount_codes = [strtoupper(trim($discount_code))];
    $shopping_cart->discount_codes = $discount_codes;
    $shopping_cart->save();

    $shopping_cart->parent_transaction->calculateTotals(false, true);
    $shopping_cart->parent_transaction->save();

    return $ret_val;
  }

  /**
   * Do inventory check against the shopping cart
   * @return array
   */
  public function doInventoryCheck(): array
  {
    $shopping_cart = $this->getCurrentCart();
    $ret_val = ['out_of_stock_lines' => [], 'system_error' => false];
    $line_items = $shopping_cart->line_items;

    /** @var TransactionLineItem $line_item */
    foreach($line_items as $line_item)
    {
      if($line_item->item->findLocationsThatCanFulfillQuantity($line_item->quantity)->isEmpty())
      {
        $ret_val['out_of_stock_lines'][] = $line_item->id;
      }
    }

    return $ret_val;
  }

  /**
   * Get the current cart
   * @return ShoppingCart
   */
  public function getCurrentCart()
  {
    $ret_val = null;

    // Find cart in session
    if(!empty(session('cart_id')))
    {
      if($this->shopping_cart instanceof ShoppingCart && $this->shopping_cart ->id == session('cart_id'))
      {
        $ret_val = $this->shopping_cart ;
      }
      else
      {
        $shopping_cart = ShoppingCart::find(session('cart_id'));
        if($shopping_cart instanceof ShoppingCart)
        {
          $this->shopping_cart  = $shopping_cart;
          $ret_val = $shopping_cart;
        }
      }
    }

    return $ret_val;
  }

  /**
   * Get shipping methods and rates
   * @param Transaction $transaction
   * @return Collection
   */
  public function getShippingMethodData(Transaction $transaction)
  {
    $methods = ShippingMethod::whereRaw('carrier_name = ? AND calculation_method = ? AND is_inactive = false', ['none', 'flat_rate'])
      ->orderBy('transit_time', 'desc')
      ->get();

    return $methods->map(function(ShippingMethod $sm) use($transaction) {

      // Check for free shipping
      $price = $sm->flat_rate;
      if(!$sm->is_express)
      {
        if(!empty(business('free_shipping_min') && is_numeric(business('free_shipping_min'))))
        {
          if($transaction->sub_total >= business('free_shipping_min'))
            $price = 0;
        }
      }

      return [
        'id' => $sm->id,
        'price' => $price,
        'name' => $sm->name
      ];
    });
  }

  /**
   * Update gift card data
   * @param ShoppingCart $shopping_cart
   * @param array $data
   * @return array
   */
  public function updateGiftCard(ShoppingCart $shopping_cart, array $data)
  {
    $ret_val = ['system_error' => false, 'removing_card' => false];

    // Handle removing gift card
    if($data['amount'] == 0 || empty($data['number']) || empty($data['amount']))
    {
      $shopping_cart->parent_transaction->gift_card_amount = null;
      $shopping_cart->parent_transaction->gift_card_number = null;
      $shopping_cart->parent_transaction->calculateTotals();
      $shopping_cart->parent_transaction->save();
      $ret_val['removing_card'] = true;
    }
    else
    {
      // Get the gift card
      $gift_card = GiftCard::where('number', $data['number'])->where('is_inactive', false)->first();

      if($gift_card instanceof GiftCard)
      {
        // Check card balance
        if($data['amount'] > $gift_card->balance || $data['amount'] < 0)
        {
          if($data['amount'] > $gift_card->balance)
            $ret_val['system_error'] = 'The amount requested on gift card exceeds the gift card balance of ' . money($gift_card->balance);
          else
            $ret_val['system_error'] = 'The amount placed on the gift card cannot be a negative number';
        }
        else
        {
          // Check exp date
          $today_date = new \DateTime();
          $exp_date = new \DateTime($gift_card->exp_date);
          if($exp_date < $today_date)
          {
            $ret_val['system_error'] = 'The gift card being used has expired.';
          }
          else
          {
            // Apply gift card settings
            $shopping_cart->parent_transaction->calculateTotals();
            $shopping_cart->parent_transaction->gift_card_amount = ($data['amount'] > ($shopping_cart->total + ($shopping_cart->parent_transaction->gift_card_amount ?? 0))) ?
              $shopping_cart->total + ($shopping_cart->parent_transaction->gift_card_amount ?? 0) : $data['amount'];

            $shopping_cart->parent_transaction->gift_card_number = $data['number'];
            $shopping_cart->parent_transaction->calculateTotals();
            $shopping_cart->parent_transaction->save();

            $ret_val = ['system_error' => false, 'gift_card' => $gift_card];
          }
        }
      }
      else
      {
        $ret_val['system_error'] = 'The gift card entered is not valid.';
      }
    }

    return $ret_val;
  }
}