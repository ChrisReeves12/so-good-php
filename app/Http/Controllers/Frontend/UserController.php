<?php
/**
 * The UserController class definition.
 *
 * Controller that handles user tasks on the frontend
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\IEntityService;
use App\Entity;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers\Frontend
 */
class UserController extends Controller
{
  /** @var IEntityService */
  protected $entityService;

  /**
   * UserController constructor.
   * @param IEntityService $entityService
   */
  public function __construct(IEntityService $entityService)
  {
    $this->entityService = $entityService;
  }

  /**
   * User registration page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function register()
  {
    return view('frontend.user.register');
  }

  /**
   * Registers the user and saves them to the database
   * @param Request $request
   * @return array
   */
  public function do_register(Request $request)
  {
    try
    {
      $ret_val = $this->entityService->createUpdate(new Entity(), ['data' => $request->get('data')], false);
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    // Find user and save them in session
    if(!empty($ret_val['entity']))
    {
      $user = $ret_val['entity'];

      session(['current_user' => [
        'user_id'    => $user->id,
        'first_name' => $user->first_name,
        'last_name'  => $user->last_name,
        'email'      => $user->email,
        'role'       => $user->role
      ]]);
    }

    return $ret_val;
  }

  /**
   * Forgot password page
   * Forgot password screen
   */
  public function forgot_password()
  {
    return view('frontend.user.forgot_password');
  }

  /**
   * Send a password reset email to the customer
   * @param Request $request
   * @return array
   */
  public function send_recovery_email(Request $request)
  {
    $ret_val = ['system_error' => false, 'errors' => false];

    try
    {
      $validator = Validator::make($request->all(), [
        'email' => 'required|email'
      ]);

      if($validator->fails())
      {
        $errors = $validator->errors()->toArray();
        $ret_val['errors'] = [];
        foreach($errors as $key => $error)
        {
          $ret_val['errors'][] = [$key => $error];
        }
      }
      else
      {
        $this->entityService->sendPasswordRecoveryEmail($request->get('email'));
      }
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Password recovery page
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function recover_password(Request $request)
  {
    try
    {
      $token = $request->query('token');

      $entity = $this->entityService->findOneByToken($token, $request->query('id'));
      if(!($entity instanceof Entity))
        throw new \Exception('An error occurred while trying to recover your account, please try again.');

      return view('frontend.user.new_password', ['user' => $entity]);
    }
    catch(\Exception $ex)
    {
      $request->session()->flash('flash_alert', $ex->getMessage());
      return redirect('/sign-in');
    }
  }

  /**
   * Reset the customer's password from the passed in form data
   * @param Request $request
   * @return array
   */
  public function reset_password(Request $request)
  {
    $ret_val['system_error'] = false;
    $ret_val['errors'] = false;

    try
    {
      $ret_val = $this->entityService->resetPassword($request->get('user_id'), $request->get('password'), $request->get('confirm_password'));

      if(!empty($ret_val['success_message']))
      {
        $request->session()->flash('flash_success', $ret_val['success_message']);
      }
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * User account page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function account()
  {
    $user = $this->entityService->findOneById(current_user('user_id'));

    return view('frontend.user.account', [
      'page_title' => 'My Account',
      'user_data' => [
        'user' => $user,
        'billing_address' => $user->billing_address,
        'shipping_address' => $user->shipping_address
      ]
    ]);
  }

  /**
   * Make updates to the account page
   * @param Request $request
   * @return array
   */
  public function do_account(Request $request)
  {
    $data = $request->all()['data'];
    $ret_val = ['system_error' => false, 'errors' => []];

    try
    {
      $user = $this->entityService->findOneById($data['id']);

      if(!($user instanceof Entity))
      {
        throw new \Exception('The user account cannot be located in our records.');
      }

      $ret_val = $this->entityService->createUpdate($user, compact('data'));
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }
}