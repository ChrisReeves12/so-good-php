<?php


namespace App\Services\Contracts;


use App\AbstractRecordType;

/**
 * Interface ICRUDRecordTypeService
 * @package App\Services\Contracts
 */
interface ICRUDRecordTypeService
{
  /**
   * Create or update record in database
   * @param $entity
   * @param array $data
   */
  public function createUpdate($entity, array $data = []);
}