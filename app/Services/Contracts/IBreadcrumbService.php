<?php
/**
 * The IBreadcrumbService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

/**
 * Interface IBreadcrumbService
 * @package App\Services\Contracts
 */
interface IBreadcrumbService
{
  /**
   * Adds an entry to the breadcrumb stack
   * @param array $entry
   */
  public function add(array $entry);
}