<?php
/**
 * The AuthController class definition.
 *
 * Handles various auth things.
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\IAuthService;
use App\Entity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package App\Http\Controllers\Frontend
 */
class AuthController extends Controller
{
  protected $authService;

  /**
   * AuthController constructor.
   * @param IAuthService $authService
   */
  public function __construct(IAuthService $authService)
  {
    $this->authService = $authService;
  }

  /**
   * The login page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    return view('frontend.auth.index');
  }

  /**
   * Handles signing a user in
   * @param Request $request
   * @return array
   */
  public function do_sign_in(Request $request)
  {
    $ret_val = $this->authService->authenticateEntity($request->get('email'), $request->get('password'), $request->get('whence'));

    // Load user in session
    if($ret_val['user'] instanceof Entity)
    {
      session(['current_user' => [
        'user_id'    => $ret_val['user']->id,
        'first_name' => $ret_val['user']->first_name,
        'last_name'  => $ret_val['user']->last_name,
        'email'      => $ret_val['user']->email,
        'role'       => $ret_val['user']->role
      ]]);

      if(!empty($ret_val['cart_id']))
      {
        session(['cart_id' => $ret_val['cart_id']]);
      }
    }

    unset($ret_val['user']);
    return $ret_val;
  }

  /**
   * Sign the user out
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function do_sign_out(Request $request)
  {
    // Remove session
    $request->session()->forget('current_user');
    $request->session()->forget('cart_id');
    return redirect('/');
  }
}