<?php
/**
 * The CartController class definition.
 *
 * Various functions for the shopping cart on the frontend
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\ICartService;
use App\Services\Contracts\IGiftCardService;
use App\Services\Contracts\ILoggerService;
use App\Services\Contracts\IPayPalService;
use App\Services\Contracts\ISalesOrderService;
use App\Http\Controllers\Controller;
use App\SalesOrder;
use App\Services\Contracts\ISubscriptionService;
use App\ShoppingCart;
use App\TransactionLineItem;
use View;
use Illuminate\Http\Request;
use Stripe\Error\Card;

/**
 * Class CartController
 * @package App\Http\Controllers\Frontend
 */
class CartController extends Controller
{
  protected $discount_codes = ['YARA10', 'CURLS10', 'YARA5OFF', 'SAVE5'];
  protected $loggerService;
  protected $salesOrderService;
  protected $cartService;
  protected $paypalService;
  protected $giftCardService;
  protected $subscriptionService;

  /**
   * CartController constructor.
   * @param ILoggerService $logger
   * @param IGiftCardService $giftCardService
   * @param ISalesOrderService $salesOrderService
   * @param ICartService $cartService
   * @param IPayPalService $payPalService
   * @param ISubscriptionService $subscriptionService
   */
  public function __construct(ILoggerService $logger, IGiftCardService $giftCardService,
                              ISalesOrderService $salesOrderService, ICartService $cartService,
                              IPayPalService $payPalService, ISubscriptionService $subscriptionService)
  {
    $this->loggerService = $logger;
    $this->salesOrderService = $salesOrderService;
    $this->cartService = $cartService;
    $this->paypalService = $payPalService;
    $this->giftCardService = $giftCardService;
    $this->subscriptionService = $subscriptionService;
  }

  public function update_gift_card(Request $request)
  {
    $shopping_cart = $this->cartService->getCurrentCart();
    return $this->cartService->updateGiftCard($shopping_cart, $request->all());
  }

  /**
   * The shopping cart home page
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index(Request $request)
  {
    // Look as to whether this should be a test order
    $shopping_cart = $this->cartService->getCurrentCart();
    if(((!empty($_COOKIE['test_order']) && $_COOKIE['test_order'] == '1') || $request->query('test_order') == 1) && current_user('role') == 'admin')
    {
      $test_order = true;
      $stripe_public_key = business('dev_stripe_public_key');
    }
    else
    {
      $test_order = false;
      $stripe_public_key = business('stripe_public_key');
    }

    if($shopping_cart)
    {
      $shopping_cart_data = $shopping_cart->getDataForCheckout($this->cartService);
    }
    else
    {
      $shopping_cart_data = [];
    }

    $shopping_cart_data['test_order'] = $test_order;

    return view('frontend.shopping_cart.index', compact('shopping_cart_data', 'stripe_public_key', 'test_order'));
  }

  /**
   * Updates the amount of each item an order can be placed for
   * @return array
   */
  public function update_orderable_quantity_on_lines()
  {
    $ret_val = ['system_error' => false];

    try
    {
      $shopping_cart = $this->cartService->getCurrentCart();
      if(!($shopping_cart instanceof ShoppingCart))
      {
        throw new \Exception('Shopping cart cannot be located.');
      }

      $ret_val['quantities'] = [];

      /** @var TransactionLineItem $line_item */
      foreach($shopping_cart->line_items as $line_item)
      {
        $ret_val['quantities'][$line_item->id] = $line_item->item->single_orderable_quantity;
      }
    }
    catch(\Exception $ex)
    {
      $this->loggerService->logException('error', $ex);
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Add a product to the shopping cart
   * @param Request $request
   * @return array
   */
  public function add(Request $request)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $transaction_line_item = $this->cartService->add(current_user('user_id'),
        $request->get('option_values'), $request->get('product_id'), $request->get('quantity'));

      // Render modal
      $ret_val['output'] = View::make('frontend.shopping_cart.add', [
        'transaction_line_item' => $transaction_line_item
      ])->render();
    }
    catch(\Exception $ex)
    {
      $this->loggerService->logException('critical', $ex);
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Receipt page
   * @param int $id
   * @return View
   */
  public function receipt($id)
  {
    $sales_order = $this->salesOrderService->findById($id);
    if(!($sales_order instanceof SalesOrder))
    {
      abort(404);
    }

    $shipping_method_display = $sales_order->line_items[0]->shipping_method->name;

    return view('frontend.shopping_cart.receipt', compact('sales_order', 'shipping_method_display'));
  }

  /**
   * Delete line item
   * @param Request $request
   * @return array
   */
  public function delete_line_item(Request $request)
  {
    $ret_val = ['system_error' => false];

    try
    {
      // Delete line item
      $this->cartService->deleteLineItem($request->get('line_id'));
    }
    catch(\Exception $ex)
    {
      $this->loggerService->logException('error', $ex);
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Update line quantity on line item
   * @param Request $request
   * @return array
   */
  public function ajax_line_qty_update(Request $request)
  {
    /** @var TransactionLineItem $transaction_line_item */
    $transaction_line_item = $this->cartService->updateLineItemQuantity($request->get('line_id'), $request->get('quantity'));
    return ['line_sub_total' => $transaction_line_item->sub_total];
  }

  /**
   * Update shipping on shopping cart
   * @param Request $request
   * @return array
   */
  public function ajax_update_shipping(Request $request)
  {
    $this->cartService->updateShippingMethod($request->get('id'));
    return ['system_error' => false];
  }

  /**
   * Updates the addresses on the cart
   * @param Request $request
   * @param return array
   * @return array
   */
  public function ajax_update_addresses(Request $request)
  {
    $ret_val = $this->cartService->updateAddresses($request->get('billing_address'),
      $request->get('shipping_address'), $request->get('shipping_address')['same_as_billing']);

    return $ret_val;
  }

  /**
   * Validates the entire checkout form before submitting order
   * @return array
   */
  public function validate_checkout_form()
  {
    $ret_val = $this->cartService->validateCheckoutForm();
    return $ret_val;
  }

  /**
   * Perform the checkout and convert the shopping cart to a sales order
   * @param Request $request
   * @return array
   * @throws \Exception
   */
  public function do_checkout(Request $request)
  {
    $ret_val = [];

    try
    {
      $test_order = ($request->get('test_order') == 'true');

      // Transform cart into sales order
      $ret_val = $this->cartService->doCheckout($test_order, $request->get('paypal_data') ?? [],
        $request->get('card_data') ?? [], ($_COOKIE['m_source'] ?? 'N/A'));

      // Subscribe customer if opted in
      try
      {
        if($request->get('subscribe_to_newsletter') === 'true' || $request->get('subscribe_to_newsletter') === true)
        {
          $this->cartService->getCurrentCart()->email;
          $this->subscriptionService->addNewsletterSubscription($this->cartService->getCurrentCart()->email, [], true, true);
        }
      }
      catch(\Exception $e)
      {
        // Ignore exception
      }

      // Delete cart from session
      $request->session()->forget('cart_id');
    }
    catch(Card $card_exception) // Card declined
    {
      $ret_val['card_error'] = 'We apologize, but your credit/debit card was declined.';
    }
    catch(\Exception $ex)
    {
      $this->loggerService->logException('critical', $ex);
    }

    return $ret_val;
  }

  /**
   * Get gift card balance
   * @param Request $request
   * @return array
   */
  public function gift_card_balance_check(Request $request)
  {
    $ret_val = ['system_error' => false];

    // Handle validation
    $this->validate($request, [
      'email_address' => 'required|email',
      'number' => 'required'
    ]);

    try
    {
      $ret_val['balance'] = $this->giftCardService->getGiftCardBalance($request->get('email_address'), $request->get('number'));
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Update user info in cart
   * @param Request $request
   * @return array
   */
  public function update_user_info(Request $request)
  {
    $user_data = [
      'first_name'   => $request->get('first_name'),
      'last_name'    => $request->get('last_name'),
      'email'        => $request->get('email'),
      'phone_number' => $request->get('phone_number')
    ];

    $ret_val = $this->cartService->updateUserInfo($user_data);
    return $ret_val;
  }

  /**
   * Send PayPal purchase
   * @param Request $request
   * @return mixed
   * @throws \Exception
   */
  public function send_paypal_purchase(Request $request)
  {
    $ret_val['paymentID'] = $this->paypalService->send_payment($this->paypalService->usePayPalSandbox($request->get('test_order')));
    return $ret_val;
  }

  /**
   * Executes the actual purchase after customer authorization
   * @param Request $request
   * @return array
   */
  public function execute_paypal_purchase(Request $request)
  {
    $ret_val = $this->paypalService->execute_payment($request->get('payerID'), $request->get('paymentID'),
      $this->paypalService->usePayPalSandbox($request->get('test_order')));

    return $ret_val;
  }

  /**
   * Update discount code in cart
   * @param Request $request
   * @return array
   */
  public function update_discount_code(Request $request)
  {
    try
    {
      $ret_val = $this->cartService->updateDiscountCode($request->get('code'), $this->discount_codes);
    }
    catch(\Exception $ex)
    {
      $this->loggerService->logException('error', $ex);
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Check inventory stock
   * @return array
   */
  public function ajax_inventory_check()
  {
    try
    {
      $ret_val = $this->cartService->doInventoryCheck();
    }
    catch(\Exception $ex)
    {
      $this->loggerService->logException('error', $ex);
    }

    return $ret_val ?? [];
  }

  /**
   * Get updates current cart
   */
  public function async_get_cart_updates()
  {
    $shopping_cart = $this->cartService->getCurrentCart();
    return (!empty($shopping_cart) ? $shopping_cart->getDataForCheckout($this->cartService) : false);
  }
}