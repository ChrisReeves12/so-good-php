<?php
/**
 * The IRepository interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface IRepository
 * @package App\Repositories\Contracts
 */
interface IRepository
{
  /**
   * Sets up the repository class
   * @param string $classname
   * @return IRepository
   */
  public function use(string $classname): IRepository;

  /**
   * Find a model by ID
   * @param int $id
   * @return mixed
   */
  public function find(int $id);

  /**
   * Find all query
   * @return Collection
   */
  public function findAll();

  /**
   * Find by criteria
   * @param array $criteria
   * @return mixed
   */
  public function findBy(array $criteria);

  /**
   * Find one by criteria
   * @param array $criteria
   * @return mixed
   */
  public function findOneBy(array $criteria);

  /**
   * Find by using SQL where
   * @param string $query
   * @param array $parameters
   * @return mixed
   */
  public function findBySql(string $query, array $parameters);

  /**
   * Find one by using SQL where
   * @param string $query
   * @param array $parameters
   * @return mixed
   */
  public function findOneBySql(string $query, array $parameters);

  /**
   * Save the model
   * @param $model
   * @return IRepository
   */
  public function save($model): IRepository;

  /**
   * Create a new model in the database
   * @param array $attributes
   * @return mixed
   */
  public function create(array $attributes);

  /**
   * Update the model with the attributes
   * @param $model
   * @param array $attributes
   * @return IRepository
   */
  public function update($model, array $attributes): IRepository;

  /**
   * Delete the model from the database
   * @param $model
   * @return IRepository
   */
  public function delete($model): IRepository;

  /**
   * Begin DB transaction
   */
  public function beginTransaction();

  /**
   * Commit DB transaction
   */
  public function commitTransaction();

  /**
   * Rollback transaction
   */
  public function rollbackTransaction();
}