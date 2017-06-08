<?php
/**
 * The IMailListService class definition.
 *
 * Interface from which all mail list services should implement
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

/**
 * Interface IMailListService
 * @package App\Services\Contracts
 */
interface IMailListService
{
  /**
   * Add contact to list
   *
   * @param string $email
   * @param string $list_name
   * @param string $name
   */
  public function addContact(string $email, string $list_name, string $name = null);

  /**
   * Update contact
   *
   * @param string $email
   * @param array $data
   */
  public function updateContact(string $email, array $data = []);

  /**
   * Remove contact
   * @param string $email
   * @param string $list_name
   */
  public function removeContact(string $email, string $list_name = '');
}