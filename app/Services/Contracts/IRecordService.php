<?php
/**
 * The IRecordService class definition.
 *
 * Interface of which all record services must implement
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\AbstractRecordType;
use Illuminate\Support\Collection;

interface IRecordService
{
  /**
   * Extracts a record to an array that is used for admin views
   * @param string $record_class
   * @param int $id
   * @return array
   */
  public function extractDataFromRecord(string $record_class, int $id): array;

  /**
   * Search records
   * @param string $record_class
   * @param string $keyword
   * @return Collection
   */
  public function searchRecords(string $record_class, string $keyword): Collection;

  /**
   * Get single record
   * @param string $record_class
   * @param int $id
   * @return AbstractRecordType
   */
  public function getSingleRecord(string $record_class, int $id): AbstractRecordType;

  /**
   * Create or update record
   * @param array $data
   * @param string $record_type
   * @param int $id
   * @return mixed
   */
  public function createUpdate(array $data, string $record_type, int $id);

  /**
   * Delete record
   * @param string $record_type
   * @param int $id
   * @return array
   */
  public function deleteRecord(string $record_type, int $id);

  /**
   * Get data for select inputs on admin record create/update pages
   * @param string $type
   * @return Collection
   */
  public function getRecordListForCreateUpdate(string $type);

  /**
   * Generate data for record listings
   * @param string $type
   * @return array
   */
  public function generateRecordListings(string $type): array;

  /**
   * Do record search on list page
   * @param string $type
   * @param string $keyword
   * @return array
   */
  public function listPageSearch(string $type, string $keyword): array;

  /**
   * Copy a record to a new one
   * @param string $type
   * @param int $id
   * @return array
   */
  public function copyRecord(string $type, int $id): array;
}