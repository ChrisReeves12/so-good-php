<?php
/**
 * The IAuthService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

/**
 * Interface IAuthService
 * @package App\Services\Contracts
 */
interface IAuthService
{
  /**
   * @param string $email
   * @param string $password
   * @param string $whence
   * @return array
   */
  public function authenticateEntity($email, $password, $whence);
}