<?php
/**
 * The IStockLocationService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface IStockLocationService
{
  /**
   * Return all stock locations
   * @return Collection
   */
  public function findAll();
}