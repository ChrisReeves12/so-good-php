<?php
/**
 * The IHomePageService interface definition.
 *
 * HomePageService Contract
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface IHomePageService
 * @package App\Services\Contracts
 */
interface IHomePageService
{
  /**
   * Get home page product listings according to type
   * @param string $type
   * @return Collection
   */
  public function getHomePageProductListings(string $type);
}