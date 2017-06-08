<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SalesOrderReceiptMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $sales_order;
    public $shipping_method_name;

    /**
     * Create a new message instance.
     */
    public function __construct($sales_order)
    {
      $this->shipping_method_name = $sales_order->line_items->first()->shipping_method->name;
      $this->sales_order = $sales_order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(business('store_email'), business('store_name'))
          ->subject('Sales Order Receipt For ' . business('store_name') . ' Order S' . $this->sales_order->id)
          ->view('mail.sales_receipt');
    }
}
