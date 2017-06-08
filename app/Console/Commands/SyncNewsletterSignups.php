<?php

namespace App\Console\Commands;

use App\Services\Contracts\IMailListService;
use App\Services\Contracts\ISubscriptionService;
use App\Subscription;
use Illuminate\Console\Command;

/**
 * Class SyncNewsletterSignups
 * @package App\Console\Commands
 */
class SyncNewsletterSignups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:sync_newsletter_signups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync newsletter signups with newsletter API.';

  /**
   * @var IMailListService
   */
    protected $mailListService;

  /**
   * @var ISubscriptionService
   */
    protected $subscriptionService;

  /**
   * Create a new command instance.
   *
   * @param IMailListService $mailListService
   * @param ISubscriptionService $subscriptionService
   */
    public function __construct(IMailListService $mailListService, ISubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->mailListService = $mailListService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        $remove_count = 0;
        $this->info('Syncing newsletter signups...');
        $subs = $this->subscriptionService->findAll();

        /** @var Subscription $sub */
        foreach($subs as $sub)
        {
          if($sub->is_inactive)
          {
            $this->mailListService->removeContact($sub->email, 'web');
            $sub->update(['synced' => false]);
            $this->line('Removed email ' . $sub->email . ' because it is marked as inactive.');
            $remove_count++;
          }
          else
          {
            if($sub->synced)
              continue;

            try
            {
              $this->mailListService->addContact($sub->email, 'web');
              $sub->update(['synced' => true]);
              $this->line('Synced ' . $sub->email . ' to newsletter.');
              $count++;
            }
            catch(\Exception $e)
            {
              $this->error($e->getMessage());
            }
          }
        }

        $this->info('Added ' . $count . ' emails to list.');
        $this->comment('Removed ' . $remove_count . ' emails.');
    }
}
