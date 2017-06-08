<?php
/**
 * The MailServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IMailService;
use Illuminate\Mail\Mailable;
use Mail;

/**
 * Class MailServiceImpl
 * @package App\Services
 */
class MailServiceImpl implements IMailService
{

  /**
   * Send message through email
   * @param $email_address
   * @param Mailable $message
   */
  public function sendEmail($email_address, Mailable $message)
  {
    Mail::to($email_address)->send($message);
  }
}