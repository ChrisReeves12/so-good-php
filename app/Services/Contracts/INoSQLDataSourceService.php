<?php
/**
 * The INoSQLDataSourceService class definition.
 *
 * The description of the class
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\NoSQLDataSourceResultSet;
use Illuminate\Support\Collection;

interface INoSQLDataSourceService
{
  /**
   * Find in the collection by a given set of criteria
   * @param string $collection
   * @param array $criteria
   * @param array $options
   * @return NoSQLDataSourceResultSet
   */
  public function findBy(string $collection, array $criteria, array $options = []): NoSQLDataSourceResultSet;

  /**
   * Update index of the given collection
   * @param string $collection
   * @param string $model_class
   * @return array
   */
  public function updateCollectionIndex(string $collection, string $model_class);
}