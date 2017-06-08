<?php

namespace App\Console\Commands;

use App\Services\Contracts\IMailListService;
use App\Services\Contracts\ISubscriptionService;
use Illuminate\Console\Command;

class SyncTransactionEmailsToNewsletterSignups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:sync_transactions_to_newsletter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take emails from transactions and move them to newsletter signups';

    /** @var ISubscriptionService */
    protected $subscriptionService;

  /**
   * Create a new command instance.
   * @param ISubscriptionService $subscriptionService
   */
    public function __construct(ISubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $this->subscriptionService->syncFromTransactions($this);
    }
}
