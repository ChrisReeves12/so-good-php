<?php
/**
 * The InvoiceServiceImpl class definition.
 *
 * Default InvoiceService implementation
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IInvoiceService;
use App\SalesOrder;
use App\Services\Contracts\ISalesOrderService;
use App\TransactionLineItem;

/**
 * Class InvoiceServiceImpl
 * @package App\Services
 */
class InvoiceServiceImpl implements IInvoiceService
{
  protected $salesOrderService;

  public function __construct(ISalesOrderService $salesOrderService)
  {
    $this->salesOrderService = $salesOrderService;
  }

  /**
   * Get data array to be used for invoice
   * @param int $sales_order_id
   * @return array
   * @throws \Exception
   */
  public function getDataForInvoice(int $sales_order_id): array
  {
    $ret_val = [];

    /** @var SalesOrder $sales_order */
    $sales_order = $this->salesOrderService->findById($sales_order_id);
    if(!($sales_order instanceof SalesOrder))
      throw new \Exception('The sales order under ID: ' . $sales_order_id . ' could not be located.');

    $ret_val = [
      'order_id' => $sales_order_id,
      'formatted_order_id' => 'S' . $sales_order_id,
      'date_created' => $sales_order->created_at->format('D. F d, Y') . ' at ' . $sales_order->created_at->format('g:i a') . ' (EST)',
      'name' => $sales_order->parent_transaction->full_name,
      'phone' => $sales_order->parent_transaction->phone_number,
      'email' => $sales_order->email,
      'billing_address' => $sales_order->billing_address,
      'shipping_address' => $sales_order->ship_to_address,
      'items' => $sales_order->line_items->map(function(TransactionLineItem $li) {
        return [
          'qty' => $li->quantity,
          'desc' => $li->item->product->name,
          'sku' => $li->item->sku,
          'details' => $li->details_for_checkout,
          'price' => $li->unit_price,
          'sub_total' => $li->sub_total
        ];
      }),
      'sub_total' => $sales_order->sub_total,
      'discount' => $sales_order->parent_transaction->discount_amount,
      'tax' => $sales_order->parent_transaction->tax,
      'shipping' => ($sales_order->parent_transaction->shipping_total > 0) ? $sales_order->parent_transaction->shipping_total : 'FREE',
      'shipping_method' => $sales_order->line_items->first()->shipping_method->name,
      'total' => $sales_order->total
    ];

    return $ret_val;
  }
}