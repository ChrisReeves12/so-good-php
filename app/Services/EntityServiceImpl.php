<?php
/**
 * The EntityServiceImpl class definition.
 *
 * Entity service implementation
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Address;
use App\Services\Contracts\ICRUDRecordTypeService;
use App\Services\Contracts\IEntityService;
use App\Services\Contracts\IMailService;
use App\Entity;
use App\Mail\PasswordRecoveryMessage;
use DB;

/**
 * Class EntityServiceImpl
 * @package App\Services
 */
class EntityServiceImpl implements IEntityService, ICRUDRecordTypeService
{
  protected $mailService;

  public function __construct(IMailService $mailService)
  {
    $this->mailService = $mailService;
  }

  /**
   * Create or update record in database
   * @param Entity $entity
   * @param array $data
   * @param bool $admin_mode
   * @return array
   */
  public function createUpdate($entity, array $data = [], bool $admin_mode = true)
  {
    $ret_val = ['system_error' => false, 'errors' => false];

    try
    {
      DB::beginTransaction();
      $data = $data['data'];

      // Get general information
      $user_info = [
        'first_name' => $data['first_name'] ?? '',
        'last_name' => $data['last_name'] ?? '',
        'email' => $data['email'] ?? '',
        'role' => $data['role'] ?? 'customer',
        'ip_address' => $data['ip_address'] ?? '0.0.0.0',
        'is_fraudulent' => $data['is_fraudulent'] ?? false,
        'token' => '',
        'phone_number' => $data['phone_number'] ?? '',
        'status' => $data['status'] ?? 'unverified',
        'is_inactive' => $data['is_inactive'] ?? false
      ];

      if(!$admin_mode)
      {
        // If this is a record being updated, do not update password if it is blank
        if(!$entity->exists || ($entity->exists && !empty($data['password'])))
        {
          $user_info['password'] = $data['password'] ?? null;
          $user_info['password_confirmation'] = $data['password_confirmation'] ?? null;
        }
      }
      else
      {
        if(!empty($data['password']))
        {
          $user_info['password'] = $data['password'];
        }
      }

      // Handle addresses
      $data['billing_address']['country'] = $data['billing_address']['country'] ?? 'US';
      $billing_address_valid = $shipping_address_valid = false;
      $billing_address_errors = [];
      if((new Address())->validate($data['billing_address'], $billing_address_errors))
      {
        $billing_address_valid = true;
        if($entity->billing_address instanceof Address)
        {
          $entity->billing_address()->update($data['billing_address']);
          $user_info['billing_address_id'] = $entity->billing_address->id;
        }
        else
        {
          $billing_address = new Address($data['billing_address']);
          $billing_address->save();
          $user_info['billing_address_id'] = $billing_address->id;
        }
      }

      // Validate shipping address, only if there is a field set
      $validate_shipping_address = false;
      $same_as_billing = $data['shipping_address']['same_as_billing'] ?? 'false';
      if($same_as_billing === 'false')
      {
        foreach($data['shipping_address'] as $value)
        {
          if(!empty($value))
          {
            $validate_shipping_address = true;
            break;
          }
        }
      }

      if($validate_shipping_address)
      {
        $shipping_address_errors = [];
        $data['shipping_address']['country'] = $data['shipping_address']['country'] ?? 'US';

        if((new Address())->validate($data['shipping_address'], $shipping_address_errors))
        {
          $shipping_address_valid = true;
          if($entity->shipping_address instanceof Address)
          {
            unset($data['shipping_address']['same_as_billing']);
            $entity->shipping_address()->update($data['shipping_address']);
            $user_info['shipping_address_id'] = $entity->shipping_address->id;
          }
          else
          {
            unset($data['shipping_address']['same_as_billing']);
            $shipping_address = new Address($data['shipping_address']);
            $shipping_address->save();
            $user_info['shipping_address_id'] = $shipping_address->id;
          }
        }
      }
      else
      {
        $shipping_address_valid = true;
      }

      // Validate the user data
      $validator_errors = [];

      // Dynamically add special validation for admin mode
      $validation_rules = $entity->getValidationRules();
      if($admin_mode && !empty($data['password']))
      {
        $validation_rules = array_merge($validation_rules, [
          'password' => 'required'
        ]);
      }
      elseif(!$admin_mode)
      {
        if(!$entity->exists || ($entity->exists && (!empty($user_info['password']) && !empty($user_info['password_confirmation']))))
        {
          $validation_rules = array_merge($validation_rules, [
            'password' => 'required|confirmed'
          ]);
        }
      }

      if(!$entity->validate($user_info, $validator_errors, $validation_rules) || !$billing_address_valid || !$shipping_address_valid)
      {
        $ret_val['errors'] = $validator_errors;

        // Add address errors
        if(!$billing_address_valid)
        {
          foreach($billing_address_errors as $attr => $error)
          {
            $ret_val['errors'][] = ['billing_address.' . key($error) => array_values($error)];
          }
        }

        if(!$shipping_address_valid && !empty($shipping_address_errors))
        {
          foreach($shipping_address_errors as $attr => $error)
          {
            $ret_val['errors'][] = ['shipping_address.' . key($error) => array_values($error)];
          }
        }
      }
      else
      {
        unset($user_info['password_confirmation']);
        $entity->fill($user_info)->save();
        $ret_val = ['errors' => false, 'system_errors' => false, 'id' => $entity->id, 'entity' => $entity];
      }

      DB::commit();
    }
    catch(\Exception $ex)
    {
      DB::rollback();
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Send recovery email for password resetting
   * @param string $email
   * @throws \Exception
   */
  public function sendPasswordRecoveryEmail(string $email)
  {
    // Find user
    $user = Entity::whereRaw('lower(email) = lower(?) AND is_inactive = false', [$email])->first();
    if(!($user instanceof Entity))
      throw new \Exception('There is no user under this email in our records.');

    // Create token
    $token = md5(time());
    $user->update(['token' => $token]);
    $forgot_url = business('site_url') . '/recover-password?token=' . urlencode($token) . '&id=' . $user->id;

    // Send recovery email
    $this->mailService->sendEmail($email, new PasswordRecoveryMessage($forgot_url, $email));
  }

  /**
   * Locate an Entity by token
   * @param string $token
   * @param int $id
   * @return Entity
   */
  public function findOneByToken(string $token, $id)
  {
    return Entity::whereRaw('id = ? AND token = ?', ['id' => $id, urldecode($token)])->first();
  }

  /**
   * Reset the user's password
   * @param int $id
   * @param string $password
   * @param string $confirm_password
   * @return array
   */
  public function resetPassword($id, string $password, string $confirm_password)
  {
    $ret_val = [];
    $user = Entity::find($id);
    if($password !== $confirm_password)
    {
      $ret_val['errors'] = 'The two passwords must match.';
    }
    else
    {
      $user->password = $password;
      $user->token = null;
      $user->save();
      $ret_val['success_message'] = 'Your password has been reset.';
    }

    return $ret_val;
  }

  /**
   * Find a user by id
   * @param int $id
   * @return Entity
   */
  public function findOneById($id)
  {
    return Entity::find($id);
  }
}