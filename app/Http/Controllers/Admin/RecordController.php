<?php
/**
 * The RecordController class definition.
 *
 * This controller handles all the various operations on record types
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\AbstractRecordType;
use App\Services\Contracts\IRecordService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class RecordController
 * @package App\Http\Controllers\Admin
 */
class RecordController extends Controller
{
  /** @var IRecordService */
  protected $recordService;

  /**
   * RecordController constructor.
   * @param IRecordService $recordService
   */
  public function __construct(IRecordService $recordService)
  {
    $this->recordService = $recordService;
  }

  /**
   * Index to the record controller, to edit or create the record
   *
   * @param string $record_type
   * @param string $id
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @throws \Exception
   */
  public function index(string $record_type, string $id = null)
  {
    $view_vars = $this->recordService->extractDataFromRecord($record_type, $id);
    $view_template = strtolower('admin.' . str_replace('-', '_', $record_type) . '.index');

    return view($view_template, ['view_vars' => $view_vars]);
  }

  /**
   * Perform record search
   * @param Request $request
   * @param string $type
   * @return array
   */
  public function record_search(Request $request, string $type)
  {
    try
    {
      $results = $this->recordService->searchRecords($type, $request->query('keyword'));
      $ret_val = ['system_error' => false, 'results' => $results->toArray()];
    }
    catch(\Exception $ex)
    {
      $ret_val = ['system_error' => $ex->getMessage()];
    }

    return $ret_val;
  }

  /**
   * Get a serialized single record from a record search field
   * @param Request $request
   * @param string $type
   * @return mixed
   */
  public function get_single_record(Request $request, string $type)
  {
    $ret_val = $this->recordService->getSingleRecord($type, $request->query('id'));
    return $ret_val;
  }

  /**
   * Creates or updates the record
   * @param string $record_type
   * @param int $id
   * @param Input $input
   * @return AbstractRecordType|array|bool
   * @throws \Exception
   */
  public function create_update(string $record_type, Input $input, $id = null)
  {
    $ret_val = ['errors' => false, 'system_error' => false];
    $data = $input::all();

    try
    {
      $ret_val = $this->recordService->createUpdate($data, $record_type, $id);
    }
    catch (\Exception $e)
    {
      $ret_val['system_error'] = $e->getMessage();
    }

    return $ret_val;
  }

  /**
   * Deletes the record
   * @param string $record_type
   * @param param int $id
   * @return array
   */
  public function delete(string $record_type, int $id)
  {
    try
    {
      $ret_val = $this->recordService->deleteRecord($record_type, $id);
    }
    catch(\Exception $e)
    {
      $ret_val['system_error'] = $e->getMessage();
    }

    return $ret_val;
  }

  /**
   * Update data on record
   * @param string $type
   * @return array
   */
  public function update_data(string $type)
  {
    $ret_val = ['system_error' => false];

    try
    {
      $records = $this->recordService->getRecordListForCreateUpdate($type);
      $ret_val['records'] = $records->map(function($r) { return ['id' => $r->id, 'label' => $r->name]; })->toArray();
    }
    catch(\Exception $ex)
    {
      $ret_val = ['system_error' => $ex->getMessage()];
    }

    return $ret_val;
  }
}