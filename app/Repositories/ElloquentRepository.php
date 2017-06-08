<?php
/**
 * The ElloquentRepository class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Repositories;

use App\Repositories\Contracts\IRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DB;

/**
 * Class ElloquentRepository
 * @package App\Services
 */
class ElloquentRepository extends AbstractRepositoryImpl
{
  /**
   * Find a model by ID
   * @param int $id
   * @return mixed
   * @throws \Exception
   */
  public function find(int $id)
  {
    if(!isset($this->class))
      throw new \Exception('There is no class set on this repository, please use the \'use\' function first to set the class for the repository.');

    return $this->class::find($id);
  }

  /**
   * Find by criteria
   * @param array $criteria
   * @return Collection
   * @throws \Exception
   */
  public function findBy(array $criteria)
  {
    if(!isset($this->class))
      throw new \Exception('There is no class set on this repository, please use the \'use\' function first to set the class for the repository.');

    $query_string = '';
    $value_set = [];

    // Go through each criteria
    foreach($criteria as $connector => $values)
    {
      foreach($values as $param => $value)
      {
        if(is_array($value))
        {
          $in_values_string = '';
          foreach($value as $in_value)
          {
            $in_values_string .= '?, ';
            $value_set[] = $in_value;
          }

          $in_values_string = rtrim($in_values_string, ', ');

          $query_string .= "{$param} IN ({$in_values_string}) {$connector} ";
        }
        else
        {
          $query_string .= "{$param} = ? {$connector} ";
          $value_set[] = $value;
        }
      }
    }

    if(!empty($connector))
    {
      $query_string = rtrim($query_string, " {$connector} ");
    }

    return $this->class::whereRaw($query_string, $value_set)->get();
  }

  /**
   * Find one by criteria
   * @param array $criteria
   * @return mixed
   */
  public function findOneBy(array $criteria)
  {
    return $this->findBy($criteria)->first();
  }

  /**
   * Update the model with the attributes
   * @param Model $model
   * @param array $attributes
   * @return IRepository
   */
  public function update($model, array $attributes): IRepository
  {
    $model->update($attributes);
    return $this;
  }

  /**
   * Save the model
   * @param Model $model
   * @return IRepository
   */
  public function save($model): IRepository
  {
    $model->save();
    return $this;
  }

  /**
   * Delete the model from the database
   * @param $model
   * @return IRepository
   */
  public function delete($model): IRepository
  {
    $model->delete();
    return $this;
  }

  /**
   * Create a new model in the database
   * @param array $attributes
   * @return mixed
   * @throws \Exception
   */
  public function create(array $attributes)
  {
    if(!isset($this->class))
      throw new \Exception('There is no class set on this repository, please use the \'use\' function first to set the class for the repository.');

    return $this->class::create($attributes);
  }

  /**
   * Find all query
   * @return Collection
   * @throws \Exception
   */
  public function findAll()
  {
    if(!isset($this->class))
      throw new \Exception('There is no class set on this repository, please use the \'use\' function first to set the class for the repository.');

    return $this->class::all();
  }

  /**
   * Find by using SQL where
   * @param string $query
   * @param array $parameters
   * @return mixed
   * @throws \Exception
   */
  public function findBySql(string $query, array $parameters)
  {
    if(!isset($this->class))
      throw new \Exception('There is no class set on this repository, please use the \'use\' function first to set the class for the repository.');

    return $this->class::whereRaw($query, $parameters)->get();
  }

  /**
   * Find one by using SQL where
   * @param string $query
   * @param array $parameters
   * @return mixed
   * @throws \Exception
   */
  public function findOneBySql(string $query, array $parameters)
  {
    if(!isset($this->class))
      throw new \Exception('There is no class set on this repository, please use the \'use\' function first to set the class for the repository.');

    return $this->class::whereRaw($query, $parameters)->get()->first();
  }

  /**
   * Begin DB transaction
   */
  public function beginTransaction()
  {
    DB::beginTransaction();
  }

  /**
   * Rollback DB transaction
   */
  public function rollbackTransaction()
  {
    DB::rollback();
  }

  /**
   * Commit DB transaction
   */
  public function commitTransaction()
  {
    DB::commit();
  }
}