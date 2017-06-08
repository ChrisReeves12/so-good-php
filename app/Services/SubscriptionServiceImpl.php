<?php
/**
 * The SubscriptionServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IMailService;
use App\Services\Contracts\ISubscriptionService;
use App\Mail\NewSubscriberMessage;
use App\Subscription;
use App\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Validator;

/**
 * Class SubscriptionServiceImpl
 * @package App\Services
 */
class SubscriptionServiceImpl implements ISubscriptionService
{
  protected $mailService;

  public function __construct(IMailService $mailService)
  {
    $this->mailService = $mailService;
  }

  /**
   * Add newsletter subscription and send confirmation email
   * @param string $email
   * @param array $form_data
   * @param bool $skip_validation
   * @param bool $skip_email
   * @throws \Exception
   */
  public function addNewsletterSubscription($email, $form_data = [], $skip_validation = false, $skip_email = false)
  {
    // Validate email
    if(empty($email))
      throw new \Exception('Please provide an email address.');

    if(!empty($form_data) && !$skip_validation)
    {
      $validator = Validator::make($form_data, [
        'email' => 'email'
      ]);

      if($validator->fails())
      {
        throw new \Exception(current($validator->errors()->toArray()['email']));
      }
    }

    // Add subscriber
    if(Subscription::whereRaw('lower(email) = ?', [strtolower($email)])->count() > 0)
    {
      throw new \Exception('You have already subscribed to our newsletter, thank you.');
    }
    else
    {
      Subscription::create([
        'email' => $email,
        'is_inactive' => false
      ]);

      if(!$skip_email)
        $this->mailService->sendEmail($email, new NewSubscriberMessage());
    }
  }

  /**
   * Find all subscribers
   * @return Collection
   */
  public function findAll()
  {
    return Subscription::all();
  }

  /**
   * Get emails from transactions and sync them to newsletter sign ups.
   * @param Command $command
   */
  public function syncFromTransactions(Command $command)
  {
    $emails_added = 0;
    $command->info('Syncing from transactions');
    $transactions = Transaction::all();

    /** @var Transaction $transaction */
    foreach($transactions as $transaction)
    {
      if(empty($transaction->email))
        continue;

      // Check if this email exists in the subscriptions already
      if(Subscription::whereRaw('lower(email) = lower(?)', [$transaction->email])->count() > 0)
        continue;

      $command->line('Adding email: ' . $transaction->email . ' to newsletter subscribers.');
      Subscription::create([
        'email' => $transaction->email,
        'is_inactive' => false
      ]);

      $emails_added++;
    }

    $command->info('Complete! Added ' . $emails_added . ' emails to list.');
  }
}