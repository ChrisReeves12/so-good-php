<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordRecoveryMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $forgot_url;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($forgot_url, $email)
    {
      $this->forgot_url = $forgot_url;
      $this->email = $email;
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        return $this->from(business('store_email'), business('store_name'))
          ->subject('Password Recovery For ' . business('store_name'))
          ->view('mail.forgot_password');
    }
}
