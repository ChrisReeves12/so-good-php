<?php
/**
 * The NoSQLDataSourceResult class definition.
 *
 * Represents one result from a NoSQLDataSource query
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App;

use Illuminate\Contracts\Support\Jsonable;

/**
 * Class NoSQLDataSourceResult
 * @package App
 */
class NoSQLDataSourceResult implements Jsonable
{
  protected $data;

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

  /*
   * Get a value from the result
   * mixed $key
   */
  public function get($key)
  {
    return $this->data[$key] ?? null;
  }

  /**
   * @param int $options
   * @return string
   */
  public function toJson($options = 0)
  {
    return json_encode($this->getData());
  }

  public function __construct(array $data)
  {
      $this->data = $data;
  }
}