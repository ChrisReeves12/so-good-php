<?php
/**
 * The AuthServiceImpl class definition.
 *
 * The description of the class
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IAuthService;
use App\Entity;
use App\ShoppingCart;
use App\Transaction;
use Hash;

/**
 * Class AuthServiceImpl
 * @package App\Services
 */
class AuthServiceImpl implements IAuthService
{
  /**
   * @param string $email
   * @param string $password
   * @param string $whence
   * @return array
   */
  public function authenticateEntity($email, $password, $whence)
  {
    $ret_val = [
      'errors' => [],
      'system_error' => false,
      'whence' => $whence
    ];

    $ret_val['user'] = Entity::whereRaw('lower(email) = ? AND is_inactive = false', [strtolower($email)])->first();

    if(!($ret_val['user'] instanceof Entity))
    {
      $ret_val['errors'][] = ['email' => ['The email and/or password is not correct.']];
    }
    else
    {
      // Check password
      if(!Hash::check($password, $ret_val['user']->password_digest))
      {
        $ret_val['errors'][] = ['email' => ['The email and/or password is not correct.']];
      }
      else
      {
        // Check if user has cart and if so, add it to session
        $transaction = Transaction::whereRaw('entity_id = ? AND transactionable_type = ?', [$ret_val['user']->id, 'ShoppingCart'])
          ->orderBy('created_at', 'desc')->first();

        if($transaction instanceof Transaction)
        {
          $shopping_cart = ShoppingCart::where('transaction_id', $transaction->id)->first();
          if($shopping_cart instanceof ShoppingCart)
          {
            $ret_val['cart_id'] = $shopping_cart->id;
          }
        }
      }
    }

    return $ret_val;
  }
}