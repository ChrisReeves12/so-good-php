<?php
/**
 * The SalesOrderController class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IInvoiceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class SalesOrderController
 * @package App\Http\Controllers\Admin
 */
class SalesOrderController extends Controller
{
  protected $invoiceService;

  /**
   * SalesOrderController constructor.
   * @param IInvoiceService $invoiceService
   */
  public function __construct(IInvoiceService $invoiceService)
  {
    $this->invoiceService = $invoiceService;
  }

  /**
   * Create invoice from given sales order
   * @param int $sales_order_id
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function invoice(int $sales_order_id)
  {
    return view('admin.sales_order.invoice', ['invoice_data' => $this->invoiceService->getDataForInvoice($sales_order_id)]);
  }
}