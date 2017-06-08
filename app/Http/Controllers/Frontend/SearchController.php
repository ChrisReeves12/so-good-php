<?php
/**
 * The SearchController class definition.
 *
 * Handles the header search
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\INoSQLDataSourceService;
use App\Http\Controllers\Controller;
use App\NoSQLDataSourceResult;
use Illuminate\Http\Request;

/**
 * Class SearchController
 * @package App\Http\Controllers\Frontend
 */
class SearchController extends Controller
{
  protected $noSqlDataSourceService;

  public function __construct(INoSQLDataSourceService $noSQLDataSourceService)
  {
    $this->noSqlDataSourceService = $noSQLDataSourceService;
  }

  /**
   * Perform search
   * @param Request $request
   * @return array
   */
  public function ajax_product_search(Request $request)
  {
    $results = $this->noSqlDataSourceService->findBy('products',
      ['*all*' => $request->query('keyword')],
      ['start' => $request->query('start'), 'max_results' => $request->query('row_count')]);

    $ret_val['docs'] = $results->getResults()->map(function(NoSQLDataSourceResult $res) {
      return([
        'slug' => $res->get('slug'),
        'image' => $res->get('image'),
        'name' => $res->get('name'),
        'store_price' => $res->get('store_price')
      ]);
    });

    return $ret_val;
  }
}