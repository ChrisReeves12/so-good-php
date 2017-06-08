<?php

namespace App\Console\Commands;

use App\Services\Contracts\IReportService;
use Illuminate\Console\Command;

class CollectShoppingCartData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:update_open_carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update open cart data';

    /** @var IReportService */
    protected $reportService;

  /**
   * Create a new command instance.
   * @param IReportService $reportService
   */
    public function __construct(IReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $this->reportService->generateRecentCartData(8, 72, $this);
    }
}
