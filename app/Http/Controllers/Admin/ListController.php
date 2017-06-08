<?php
/**
 * The ListController class definition.
 *
 * This controller manages the list of records in the admin panel
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Admin;

use App\Services\Contracts\IRecordService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class ListController
 * @package App\Http\Controllers\Admin
 */
class ListController extends Controller
{
  /** @var IRecordService */
  protected $recordService;

  /**
   * ListController constructor.
   * @param IRecordService $recordService
   */
  public function __construct(IRecordService $recordService)
  {
    $this->recordService = $recordService;
  }

  /**
   * Main entry point
   * @param string $type
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @throws \Exception
   */
  public function index(string $type)
  {
    $ret_val = $this->recordService->generateRecordListings($type);
    return view('admin.list.index', [
      'record_data' => $ret_val['record_data'],
      'record_type' => $ret_val['record_type'],
      'paginator' => $ret_val['paginator'],
      'can_be_duplicated' => $ret_val['can_be_duplicated']
    ]);
  }

  /**
   * Copy a record
   * @param string $type
   * @param int $id
   * @return array
   */
  public function copy_record(string $type, int $id)
  {
    $ret_val = $this->recordService->copyRecord($type, $id);
    return $ret_val;
  }

  /**
   * Perform search
   * @param Request $request
   * @return array
   */
  public function search(Request $request)
  {
    try
    {
      $ret_val = $this->recordService->listPageSearch($request->query('type'), $request->query('keyword'));
      return view('admin.list.search_results_modal', ['response' => $ret_val]);
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }
}