<?php

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\ILoggerService;
use App\Services\Contracts\IReportService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

/**
 * The HomeController class definition.
 *
 * The main controller of the Admin section
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/
class HomeController extends Controller
{
  protected $reportService;
  protected $loggerService;

  /**
   * HomeController constructor.
   * @param IReportService $reportService
   * @param ILoggerService $loggerService
   */
  public function __construct(IReportService $reportService, ILoggerService $loggerService)
  {
    $this->reportService = $reportService;
    $this->loggerService = $loggerService;
  }

  /**
   * Home page
   */
  public function index()
  {
    $day_start = Carbon::now(business('timezone'))->startOfDay()->timezone('UTC');
    $day_end = Carbon::now(business('timezone'))->endOfDay()->timezone('UTC');
    $week_start = Carbon::now(business('timezone'))->startOfWeek()->timezone('UTC');
    $week_end = Carbon::now(business('timezone'))->endOfWeek()->timezone('UTC');
    $month_start = Carbon::now(business('timezone'))->startOfMonth()->timezone('UTC');
    $month_end = Carbon::now(business('timezone'))->endOfMonth()->timezone('UTC');
    $year_start = Carbon::now(business('timezone'))->startOfYear()->timezone('UTC');
    $year_end = Carbon::now(business('timezone'))->endOfYear()->timezone('UTC');

    // Calculate stats
    $stats = [
      'total_revenue' => $this->reportService->getTotalStoreRevenue(),
      'year_revenue'  => $this->reportService->getTotalStoreRevenueInScope($year_start, $year_end),
      'month_revenue' => $this->reportService->getTotalStoreRevenueInScope($month_start, $month_end),
      'week_revenue'  => $this->reportService->getTotalStoreRevenueInScope($week_start, $week_end),
      'today_revenue' => $this->reportService->getTotalStoreRevenueInScope($day_start, $day_end)
    ];

    $today_orders = $this->reportService->getAdminHomeTodayOrders();
    $pending_orders = $this->reportService->geAdminHometPendingOrders();
    $today_users = $this->reportService->getAdminHomeTodayUsers();

    return view('admin.home.index', compact('stats', 'today_orders', 'pending_orders', 'today_users'));
  }

  /**
   * Show error and information logs
   */
  public function logs()
  {
    // Get error logs
    $log_data = [
      'errors' => $this->loggerService->findByTypeArray(['EMERGENCY', 'ERROR', 'CRITICAL'], 'frontend')
    ];

    return view('admin.logs.index', compact('log_data'));
  }
}