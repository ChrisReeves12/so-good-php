<?php
/**
 * The ReportsController class definition.
 *
 * Handles generating various reports
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IReportService;
use App\Http\Controllers\Controller;

/**
 * Class ReportsController
 * @package App\Http\Controllers\Admin
 */
class ReportsController extends Controller
{
  protected $reportService;

  /**
   * ReportsController constructor.
   * @param IReportService $reportService
   */
  public function __construct(IReportService $reportService)
  {
    $this->reportService = $reportService;
  }

  /**
   * Print a CSV file of the email subscribers
   * @param string $type
   */
  public function email_sub_report(string $type)
  {
    return response($this->reportService->generateEmailSubReport($type), 200)->header('Content-Type', 'text/csv');
  }
}