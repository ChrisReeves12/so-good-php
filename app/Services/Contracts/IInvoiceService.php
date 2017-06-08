<?php
/**
 * The IInvoiceService interface definition.
 *
 * InvoiceService Contract
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\SalesOrder;

/**
 * Interface IInvoiceService
 * @package App\Services\Contracts
 */
interface IInvoiceService
{
  /**
   * Get data array to be used for invoice
   * @param int $sales_order_id
   * @return array
   */
  public function getDataForInvoice(int $sales_order_id): array;
}