<?php

/**
 * The RepositoryImpl class definition.
 *
 * The description of the class
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Repositories;

use App\Repositories\Contracts\IRepository;

/**
 * Class RepositoryImpl
 * @package App\Repositories
 */
abstract class AbstractRepositoryImpl implements IRepository
{
  protected $class;

  /**
   * Sets up the repository class
   * @param string $classname
   * @return IRepository
   */
  public function use(string $classname): IRepository
  {
    $this->class = $classname;
    return $this;
  }

  /**
   * Find a model by ID
   * @param int $id
   * @return mixed
   */
  abstract public function find(int $id);

  /**
   * Find by criteria
   * @param array $criteria
   * @return mixed
   */
  abstract public function findBy(array $criteria);

  /**
   * Find by using SQL where
   * @param string $query
   * @param array $parameters
   * @return mixed
   */
  abstract public function findBySql(string $query, array $parameters);

  /**
   * Find one by using SQL where
   * @param string $query
   * @param array $parameters
   * @return mixed
   */
  abstract public function findOneBySql(string $query, array $parameters);

  /**
   * Find one by criteria
   * @param array $criteria
   * @return mixed
   */
  abstract public function findOneBy(array $criteria);

  /**
   * Begin DB transaction
   */
  abstract public function beginTransaction();

  /**
   * Rollback DB transaction
   */
  abstract public function rollbackTransaction();

  /**
   * Commit DB transaction
   */
  abstract public function commitTransaction();
}