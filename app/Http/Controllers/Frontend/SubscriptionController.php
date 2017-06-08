<?php
/**
 * The SubscriptionController class definition.
 *
 * Handles newsletter and other subscriptions
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\ISubscriptionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class SubscriptionController
 * @package App\Http\Controllers\Frontend
 */
class SubscriptionController extends Controller
{
  protected $subscriptionService;

  /**
   * SubscriptionController constructor.
   * @param ISubscriptionService $subscriptionService
   */
  public function __construct(ISubscriptionService $subscriptionService)
  {
    $this->subscriptionService = $subscriptionService;
  }

  /**
   * Add a subscriber to the newsletter
   * @param Request $request
   * @return array
   */
  public function newsletter_add(Request $request)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $this->subscriptionService->addNewsletterSubscription($request->get('email'), $request->all());
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Add a subscriber to the newsletter from the popup
   * @param Request $request
   * @return array
   */
  public function newsletter_popup(Request $request)
  {
    $ret_val = ['system_error' => false];
    $email = $request->get('data')[0]['value'];

    try
    {
      $this->subscriptionService->addNewsletterSubscription($email, compact('email'));
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return ['response_text' => !$ret_val['system_error'] ? 'Thank you for registering!' : $ret_val['system_error']];
  }
}