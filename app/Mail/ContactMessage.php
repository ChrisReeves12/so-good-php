<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessage extends Mailable
{
  use Queueable, SerializesModels;

  public $body_message;
  public $from_email;
  public $user_name;

  /**
   * Create a new message instance.
   *
   */
  public function __construct($name, $message, $from_email)
  {
    $this->user_name = $name;
    $this->body_message = $message;
    $this->from_email = $from_email;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this
      ->from(business('store_email'))
      ->subject('Message from ' . $this->user_name)
      ->view('mail.contact_message');
  }
}
