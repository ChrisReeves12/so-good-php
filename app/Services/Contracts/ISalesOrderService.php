<?php
/**
 * The ISalesOrderService interface definition.
 *
 * Service from which all Sales Order services should derive
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;
use App\SalesOrder;
use App\ShoppingCart;

/**
 * Interface ISalesOrderService
 * @package App\Services\Contracts
 */
interface ISalesOrderService
{
  /**
   * Create or update sales order
   * @param $sales_order
   * @param array $data
   */
  public function createUpdate($sales_order, array $data = []);

  /**
   * Transform cart to sales order
   * @param ShoppingCart $cart
   * @param string $payment_method
   * @param string $marketing_source
   * @return SalesOrder
   */
  public function transformToSalesOrder(ShoppingCart $cart, $payment_method, $marketing_source = null);

  /**
   * Find a sales order by id
   * @param int $id
   * @return mixed
   */
  public function findById(int $id);

  /**
   * Validator to check if the payment method is valid
   * @param string $attribute
   * @param array $value
   * @param array $parameters
   * @return bool
   */
  public function validatePaymentMethod($attribute, $value, $parameters);

  /**
   * Update sales order search
   */
  public function updateSalesOrderIndex();
}