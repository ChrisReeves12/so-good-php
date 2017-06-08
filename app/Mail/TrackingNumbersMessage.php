<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackingNumbersMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $sales_order;
    public $shipping_method_name;
    public $tracking_numbers;

    /**
     * Create a new message instance.
     */
    public function __construct($sales_order)
    {
      $this->sales_order = $sales_order;
      $this->tracking_numbers = json_decode($sales_order->tracking_numbers, true);
      $this->shipping_method_name = $sales_order->line_items->first()->shipping_method->name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this
        ->from(business('store_email'), business('store_name'))
        ->subject('Your Order  (S' . $this->sales_order->id . ') Has Been Shipped!')
        ->view('mail.shipping_notice');
    }
}
