<?php
/**
 * The AbstractRecordType class definition.
 *
 * The description of the class
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;
use Validator;
use Schema;

abstract class  AbstractRecordType extends Model
{
  public function getViewParameters(): array
  {
    return $this->getColumnNames();
  }

  public function getListSortProcedure()
  {
    return ['created_at', 'desc'];
  }

  public function setIsInactiveAttribute($value)
  {
    if(is_string($value))
    {
      $this->attributes['is_inactive'] = ($value === 'true');
    }
    else
    {
      $this->attributes['is_inactive'] = empty($value) ? false : boolval($value);
    }
  }

  public function getIsInactiveDisplayAttribute()
  {
    return $this->attributes['is_inactive'] ? 'Yes' : 'No';
  }

  /**
   * Gets any extra data that should be used when record is viewed in the admin
   * @return array
   */
  public function getExtraData(): array
  {
    return [];
  }

  public function getColumnNames()
  {
    return Schema::getColumnListing($this->getTable());
  }

  public function getReadOnlyParams(): array
  {
    return ['created_at', 'updated_at'];
  }

  public function canBeDuplicated()
  {
    return false;
  }

  public function createUpdate($data = [])
  {
    return false;
  }

  public function getValidationRules($data = []): array
  {
    return [];
  }

  public function getPreDeleteFields(): array
  {
    return [];
  }

  public function getValidationMessages(): array
  {
    return [];
  }

  public function toSolrDocument(DocumentInterface $doc): DocumentInterface
  {
    return $doc;
  }

  public function getPostSaveFields(): array
  {
    return [];
  }

  public function validate($data, &$errors = [], $validation_rules = null)
  {
    $is_valid = true;

    $validator = Validator::make($data, ($validation_rules ?? $this->getValidationRules($data)), $this->getValidationMessages());
    if ($validator->fails())
    {
      $is_valid = false;
      $error_keys = array_keys($validator->errors()->toArray());
      foreach ($error_keys as $error_key)
      {
        $errors[] = [$error_key => $validator->errors()->get($error_key)];
      }
    }

    return $is_valid;
  }
}