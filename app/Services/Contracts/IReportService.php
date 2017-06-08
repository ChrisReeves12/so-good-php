<?php
/**
 * The IReportService interface definition.
 *
 * ReportService Contract
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Interface IReportService
 * @package App\Services\Contracts
 */
interface IReportService
{
  /**
   * Gets sales order data for display on Admin home for today's orders
   * @return Collection   * @return Collection
   */
  public function getAdminHomeTodayOrders(): Collection;

  /**
   * Get total store revenue in sales
   * @return float
   */
  public function getTotalStoreRevenue();

  /**
   * Get total revenue within timeframe
   * @param $begin_time
   * @param $end_time
   * @return float
   */
  public function getTotalStoreRevenueInScope($begin_time, $end_time);

  /**
   * Get orders in pending status
   * @return Collection
   */
  public function geAdminHometPendingOrders(): Collection;

  /**
   * Get today's users for admin home page
   * @return Collection
   */
  public function getAdminHomeTodayUsers(): Collection;

  /**
   * Generates a CSV output of the type of email subscriber report
   * @param string $type
   * @return string
   */
  public function generateEmailSubReport(string $type);

  /**
   * Generate data for recent open shopping carts
   * @param int $min_hours
   * @param int $max_hours
   * @param Command $command
   */
  public function generateRecentCartData(int $min_hours, int $max_hours, Command $command);
}