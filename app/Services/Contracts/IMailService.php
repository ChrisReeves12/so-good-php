<?php
/**
 * The IMailService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use Illuminate\Mail\Mailable;

/**
 * Interface IMailService
 * @package App\Services\Contracts
 */
interface IMailService
{
  /**
   * Send message through email
   * @param $email_address
   * @param Mailable $message
   */
  public function sendEmail($email_address, Mailable $message);
}