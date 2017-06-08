<?php
/**
 * The IEntityService interface definition.
 *
 * Interface from which all entity services should implement
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\Entity;

/**
 * Interface IEntityService
 * @package App\Services\Contracts
 */
interface IEntityService
{
  /**
   * @param $entity
   * @param array $data
   * @param bool $admin_mode
   * @return array
   */
  public function createUpdate($entity, array $data = [], bool $admin_mode = true);

  /**
   * Send recovery email for password resetting
   * @param string $email
   */
  public function sendPasswordRecoveryEmail(string $email);

  /**
   * Locate an Entity by token
   * @param string $token
   * @param int $id
   * @return Entity
   */
  public function findOneByToken(string $token, $id);

  /**
   * Reset the user's password
   * @param int $id
   * @param string $password
   * @param string $confirm_password
   * @return array
   */
  public function resetPassword($id, string $password, string $confirm_password);

  /**
   * Find a user by id
   * @param int $id
   * @return Entity
   */
  public function findOneById($id);
}