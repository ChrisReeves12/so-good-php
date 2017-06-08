<?php
/**
 * The ISubscriptionService interface definition.
 *
 * The description of the interface
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Interface ISubscriptionService
 * @package App\Services\Contracts
 */
interface ISubscriptionService
{
  /**
   * Add newsletter subscription and send confirmation email
   * @param string $email
   * @param array $form_data
   * @param bool $skip_validation
   * @param bool $skip_email
   * @return
   */
  public function addNewsletterSubscription($email, $form_data = [], $skip_validation = false, $skip_email = false);

  /**
   * Find all subscribers
   * @return Collection
   */
  public function findAll();

  /**
   * Get emails from transactions and sync them to newsletter sign ups.
   * @param Command $command
   */
  public function syncFromTransactions(Command $command);
}