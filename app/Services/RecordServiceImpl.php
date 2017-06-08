<?php
/**
 * The RecordServiceImpl class definition.
 *
 * Handles various tasks with records in admin
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\AbstractRecordType;
use App\Services\Contracts\IAdminSearchable;
use App\Services\Contracts\ICRUDRecordTypeService;
use App\Services\Contracts\IRecordCopyable;
use App\Services\Contracts\IRecordService;
use App\RecordField;
use App\RecordType;
use Illuminate\Support\Collection;
use DB;

/**
 * Class RecordServiceImpl
 * @package App\Services
 */
class RecordServiceImpl implements IRecordService
{
  /**
   * Extracts a record to an array that is used for admin views
   * @param string $record_class
   * @param int $id
   * @return array
   * @throws \Exception
   */
  public function extractDataFromRecord(string $record_class, int $id = null): array
  {
    $ret_val = ['record_data' => [], 'extra_data' => []];

    $formatted_record_type = ucfirst(camel_case(str_replace('-', '_', $record_class)));

    $formatted_record_type_class = "\\App\\{$formatted_record_type}";
    if (!class_exists($formatted_record_type_class))
    {
      throw new \Exception("The record type: {$formatted_record_type} does not exist.");
    }

    $record = empty($id) ? new $formatted_record_type_class() : $formatted_record_type_class::find($id);
    if (empty($record))
    {
      throw new \Exception("Cannot find {$formatted_record_type} with id: {$id}");
    }

    // Must be a record type class
    if (!($record instanceof AbstractRecordType))
    {
      throw new \Exception("The class {$formatted_record_type} must be an instance of App\\AbstractRecordType to be used as an editable record.");
    }

    $this->_applyParamsToCollection($ret_val['record_data'], $record);
    $ret_val['extra_data'] = $record->getExtraData();

    return $ret_val;
  }

  /**
   * Perform a record search
   * @param string $record_class
   * @param string $keyword
   * @return Collection
   * @throws \Exception
   */
  public function searchRecords(string $record_class, string $keyword): Collection
  {
    $results = new Collection();
    $qualified_record_class = '\\App\\' . ucfirst($record_class);
    if(!class_exists($qualified_record_class))
      throw new \Exception('The record class ' . $qualified_record_class . ' does not exist.');

    // Find record type
    $record_type = RecordType::where('model', $record_class)->first();
    if(!($record_type instanceof RecordType))
      throw new \Exception('The record type ' . $record_class . ' could not be located.');

    // Get searchable fields
    $searchable_fields = $record_type->record_fields->filter(function(RecordField $rf) {
      return($rf->searchable);
    });

    // Find the record
    $keyword = addslashes($keyword);

    $records = $qualified_record_class::all();

    foreach($records as $record)
    {
      foreach($searchable_fields as $searchable_field)
      {
        $rec_field_value = $record->{$searchable_field->formula};

        // Add record if there is a match
        $similar_percentage = 0;
        similar_text(strtolower($keyword), strtolower($rec_field_value), $similar_percentage);
        if($similar_percentage > 24)
        {
          if(!empty($record->display_record_name))
            $label = $record->display_record_name;
          else
            $label = $record->id;

          $display_image = '';
          if(!empty($record->display_record_image))
            $display_image = $record->display_record_image;

          // Make sure that the collection doesn't already contain the object
          if($results->filter(function($r) use($record, $label, $display_image) { return($r['id'] == $record->id); })->isEmpty())
          {
            $results->push(['label' => $label, 'id' => $record->id, 'img' => $display_image]);
          }
        }
      }
    }

    return $results;
  }

  /**
   * Get single record
   * @param string $record_class
   * @param int $id
   * @return AbstractRecordType
   */
  public function getSingleRecord(string $record_class, int $id): AbstractRecordType
  {
    $qualified_record_class = '\\App\\' . ucfirst($record_class);
    $result = $qualified_record_class::find($id);

    if(($result instanceof AbstractRecordType) && !empty($result->getViewParameters()))
    {
      // Get view parameters
      $ret_val = [];
      $this->_applyParametersToCollection($ret_val, $result);
    }
    else
    {
      $ret_val = $result;
    }

    return $ret_val;
  }

  /**
   * Create or update record
   * @param array $data
   * @param string $record_type
   * @param int|null $id
   * @return AbstractRecordType|array|bool
   * @throws \Exception
   */
  public function createUpdate(array $data, string $record_type, int $id = null)
  {
    try
    {
      DB::beginTransaction();

      $ret_val = ['errors' => false, 'system_error' => false];
      $using_record_service = false;
      $is_valid = true;

      $record_type = ucfirst($record_type);
      $record_class = "\\App\\{$record_type}";
      if(!class_exists($record_class))
      {
        throw new \Exception('Record type: ' . $record_class . ' does not exist. The class does not exist.');
      }

      // Get if we should use the generic create update function or the one on the record
      $record = (empty($id) || $id == 'undefined') ? new $record_class() : $record_class::find($id);
      if(empty($record))
      {
        throw new \Exception('Could not create or find record.');
      }

      if(!($record instanceof AbstractRecordType))
      {
        throw new \Exception('The record type being saved must extend the AbstractRecordType.');
      }

      // Find service class and use create update method on it if it exists
      $result = false;
      $crud_service_overrides = service_config('record_service_crud_overrides');
      $service_class = !empty($crud_service_overrides[get_class($record)]) ? $crud_service_overrides[get_class($record)] : null;
      if(!empty($service_class))
      {
        $service = app()->make($service_class);
        if($service instanceof ICRUDRecordTypeService)
        {
          $result = $service->createUpdate($record, $data);
        }
      }


      if(!$result)
      {
        // Validate
        $validation_errors = [];
        if(!empty($record->getValidationRules($data['data'])))
        {
          $is_valid = $record->validate($data['data'], $validation_errors);
        }

        if($is_valid)
        {
          // Handle delete fields
          if(!empty($record->getPreDeleteFields()))
          {
            foreach($record->getPreDeleteFields() as $delete_field)
            {
              $record->{$delete_field}()->delete();
            }
          }

          // Apply attributes to the record
          $result = $this->_applyAttributesToRecord($record, $data['data']);
        }
        else
        {
          // Validation failed
          $ret_val['errors'] = $validation_errors;
          DB::rollback();
        }
      }
      else
      {
        $using_record_service = true;
      }

      // If there is no CRUD strategy for the record, perform all the validation here
      if(!$using_record_service)
      {
        if($is_valid)
        {
          $record->save();

          // Post save fields
          if(!empty($record->getPostSaveFields()))
          {
            foreach($record->getPostSaveFields() as $post_save_field)
            {
              if(in_array($post_save_field, $record->getReadOnlyParams()))
              {
                continue;
              }

              $record->{$post_save_field} = $data['data'][$post_save_field];
            }
          }

          DB::commit();

          $ret_val = ['errors' => false, 'system_error' => false, 'id' => $result->id];
        }
      }
      else
      {
        $ret_val = $result;
        if(!empty($ret_val['system_error']))
        {
          throw new \Exception($ret_val['system_error']);
        }

        if(empty($ret_val['errors']))
        {
          DB::commit();
        }
        else
        {
          DB::rollback();
        }
      }
    }
    catch(\Exception $ex)
    {
      DB::rollback();
      throw $ex;
    }

    return $ret_val;
  }

  /**
   * Delete record
   * @param string $record_type
   * @param int $id
   * @return array
   * @throws \Exception
   */
  public function deleteRecord(string $record_type, int $id)
  {
    $ret_val = ['errors' => false, 'system_error' => false];

    DB::beginTransaction();

    // Find record
    $record_class = "\\App\\{$record_type}";
    if(!class_exists($record_class))
    {
      DB::rollback();
      throw new \Exception('The record type: ' . $record_class . ' does not exist. The class does not exist.');
    }

    $record = $record_class::find($id);
    if(empty($record))
    {
      DB::rollback();
      throw new \Exception('The ' . $record_type . ' with id: ' . $id . ' could not be located in the database.');
    }

    if(!($record instanceof AbstractRecordType))
    {
      DB::rollback();
      throw new \Exception('The class ' . $record_class . ' must implement App\\AbstractRecordType to be used in the RecordController methods.');
    }

    $record->delete();
    DB::commit();

    return $ret_val;
  }

  /**
   * Get list of records for create update select inputs
   * @param string $type
   * @throws \Exception
   * @return Collection
   */
  public function getRecordListForCreateUpdate(string $type)
  {
    $record_class = '\\App\\' . ucfirst($type);

    if(!class_exists($record_class))
      throw new \Exception('Cannot locate class for record type ' . $record_class);

    $record = new $record_class();
    $inactive_check = in_array('is_inactive', DB::getSchemaBuilder()->getColumnListing($record->getTable()));
    if($inactive_check)
    {
      $records = $record_class::where('is_inactive', false)->get();
    }
    else
    {
      $records = $record_class::all();
    }

    return $records;
  }

  /**
   * Recursively add parameters
   * @param array $target_collection
   * @param AbstractRecordType $model
   */
  private function _applyParamsToCollection(array &$target_collection, AbstractRecordType $model)
  {
    // Go throw view parameters
    foreach ($model->getViewParameters() as $parameter)
    {
      $has_been_set = false;
      $value = $model->{$parameter};
      if ($value instanceof Collection)
      {
        // Recursively add attributes of models in array
        if($value->first() instanceof AbstractRecordType)
        {
          $collection_models = $value->values();
          foreach($collection_models as $key => $collection_model)
          {
            if(!isset($target_collection[$parameter][$key]))
              $target_collection[$parameter][$key] = [];

            $this->_applyParamsToCollection($target_collection[$parameter][$key], $collection_model);
            $has_been_set = true;
          }
        }
        else
        {
          $value = $value->values()->toArray();
        }
      }
      elseif($value instanceof AbstractRecordType) // Recursively add attributes from models
      {
        if(!isset($target_collection[$parameter]))
          $target_collection[$parameter] = [];

        $this->_applyParamsToCollection($target_collection[$parameter], $value);
        $has_been_set = true;
      }

      if(!$has_been_set)
        $target_collection[$parameter] = $value;
    }
  }

  /**
   * Recursively add parameters
   * @param array $target_collection
   * @param AbstractRecordType $model
   */
  private function _applyParametersToCollection(array &$target_collection, AbstractRecordType $model)
  {
    // Go throw view parameters
    foreach ($model->getViewParameters() as $parameter)
    {
      $has_been_set = false;
      $value = $model->{$parameter};
      if ($value instanceof Collection)
      {
        // Recursively add attributes of models in array
        if($value->first() instanceof AbstractRecordType)
        {
          $collection_models = $value->values();
          foreach($collection_models as $key => $collection_model)
          {
            if(!isset($target_collection[$parameter][$key]))
              $target_collection[$parameter][$key] = [];

            $this->_applyParametersToCollection($target_collection[$parameter][$key], $collection_model);
            $has_been_set = true;
          }
        }
        else
        {
          $value = $value->values()->toArray();
        }
      }
      elseif($value instanceof AbstractRecordType) // Recursively add attributes from models
      {
        if(!isset($target_collection[$parameter]))
          $target_collection[$parameter] = [];

        $this->_applyParametersToCollection($target_collection[$parameter], $value);
        $has_been_set = true;
      }

      if(!$has_been_set)
        $target_collection[$parameter] = $value;
    }
  }

  /**
   * Copy a record
   * @param string $type
   * @param int $id
   * @return array
   */
  public function copyRecord(string $type, int $id): array
  {
    $ret_val = ['system_error' => false];

    try
    {
      DB::beginTransaction();
      $record_type = RecordType::where('name', ucfirst($type))->first();
      if(empty($record_type))
        throw new \Exception('Cannot find record type for: ' . $type);

      $record_class = "App\\{$type}";
      if(!class_exists($record_class))
        throw new \Exception('Cannot find class ' . $record_class);

      $record_to_copy = $record_class::find($id);
      if(empty($record_to_copy))
        throw new \Exception('Could not find record to copy of id: ' . $id);

      $record_has_inactive = in_array('is_inactive', DB::getSchemaBuilder()->getColumnListing($record_to_copy->getTable()));

      // Check for service that should override default copy routine
      $copy_override_interface = service_config('record_copy_overrides')[$record_class] ?? null;
      if(!empty($copy_override_interface) && interface_exists($copy_override_interface) &&
        (($service = app()->make($copy_override_interface)) instanceof IRecordCopyable))
      {
        /** @var IRecordCopyable $service */
        $ret_val = $service->copyRecord($record_to_copy, $record_type, $record_has_inactive);
      }
      else // Use default copy routine
      {
        $copied_record = $record_to_copy->replicate();
        if($record_has_inactive)
          $copied_record->is_inactive = true;

        $copied_record->save();
        $ret_val['edit_url'] = $record_type->edit_url . '/' . $copied_record->id;
      }

      DB::commit();
    }
    catch(\Exception $ex)
    {
      DB::rollback();
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }

  /**
   * Generate data for record listings
   * @param string $type
   * @return array
   * @throws \Exception
   */
  public function generateRecordListings(string $type): array
  {
    $ret_val = ['record_data' => [], 'record_type' => null, 'can_be_duplicated' => false, 'paginator' => null];
    $record_type = RecordType::where('name', ucfirst($type))->first();
    if(!($record_type instanceof RecordType))
      throw new \Exception('Record type under name ' . $type . ' could not be located.');

    $record_class = '\\App\\' . $record_type->model;

    if(!in_array('App\AbstractRecordType', class_parents($record_class)))
      throw new \Exception('The class record type ' . $record_type . ' must be an instance of AbstractRecordType.');

    // Get records
    $ret_val['can_be_duplicated'] = with(new $record_class)->canBeDuplicated();
    $order_by_key = with(new $record_class)->getListSortProcedure()[0];
    $order_by_direction = with(new $record_class)->getListSortProcedure()[1];

    $records = $record_class::orderBy($order_by_key, $order_by_direction)->paginate(business('admin_listings_count'));
    $ret_val['record_data'] = $records->map(function($r) use ($record_type)
    {
      $local_ret_val['ID'] = $r->id;

      /** @var RecordField $record_field */
      foreach($record_type->record_fields as $record_field)
      {
        $local_ret_val[$record_field->name] = $r->{$record_field->formula};
      }

      return $local_ret_val;
    });

    $ret_val['record_type'] = $record_type;
    $ret_val['paginator'] = $records;

    return $ret_val;
  }

  /**
   * Handle record list page search
   * @param string $type
   * @param string $keyword
   * @return array
   * @throws \Exception
   */
  public function listPageSearch(string $type, string $keyword): array
  {
    $ret_val = ['system_error' => false];

    $record_type = RecordType::where('model', ucfirst($type))->first();
    if(!($record_type instanceof RecordType))
      throw new \Exception('Could not find record type: ' . $record_type);

    $record_class = 'App\\' . $record_type->model;
    if(!class_exists($record_class))
      throw new \Exception('Cannot find class: ' . $record_class);

    $search_service_overrides = service_config('record_service_list_search_overrides');
    if(empty($search_service_overrides[$record_class]))
      throw new \Exception('The current list does not have admin search implemented yet...');

    $record_service_interface = $search_service_overrides[$record_class];
    $record_service = app()->make($record_service_interface);
    if(!($record_service instanceof IAdminSearchable))
      throw new \Exception('Record service search overrides must implement IAdminSearchable...');

    $results = $record_service->handleAdminSearch($keyword);
    $ret_val['results'] = $results;

    return $ret_val;
  }

  /**
   * Apply attributes to record
   * @param AbstractRecordType $record
   * @param array $data_set
   * @return AbstractRecordType
   */
  private function _applyAttributesToRecord(AbstractRecordType $record, array $data_set): AbstractRecordType
  {
    $keys = array_keys($data_set);
    foreach ($keys as $key)
    {
      if(in_array($key, $record->getReadOnlyParams()) || in_array($key, $record->getPostSaveFields()) || $key === 'id')
        continue;

      if(!method_exists($record, camel_case("set_{$key}_attribute")) && !in_array($key, $record->getColumnNames()))
        continue;

      $value = $data_set[$key];

      // Basic values are added to their corresponding attributes
      $record->{$key} = $value;
    }

    return $record;
  }
}