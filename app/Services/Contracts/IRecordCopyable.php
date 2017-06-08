<?php
/**
 * The IRecordCopyable interface definition.
 *
 * Interface used to designate a service that copy records
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\RecordType;

/**
 * Interface IRecordCopyable
 * @package App\Services\Contracts
 */
interface IRecordCopyable
{
  /**
   * Copies a single record
   * @param $record
   * @param RecordType $recordType
   * @param bool $record_has_inactive
   * @return array
   */
  public function copyRecord($record, RecordType $recordType, $record_has_inactive = false): array;
}