<?php
/**
 * The HomePageServiceImpl class definition.
 *
 * Default HomePageService implementation
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IHomePageService;
use App\Services\Contracts\INoSQLDataSourceService;
use Illuminate\Support\Collection;

/**
 * Class HomePageServiceImpl
 * @package App\Services
 */
class HomePageServiceImpl implements IHomePageService
{
  protected $noSQLDataSourceService;

  /**
   * HomePageServiceImpl constructor.
   * @param INoSQLDataSourceService $noSQLDataSourceService
   */
  public function __construct(INoSQLDataSourceService $noSQLDataSourceService)
  {
    $this->noSQLDataSourceService = $noSQLDataSourceService;
  }

  /**
   * Get home page product listings according to type
   * @param string $type
   * @return Collection
   */
  public function getHomePageProductListings(string $type)
  {
    $ret_val = new Collection();

    switch($type)
    {
      // Get popular wigs listings
      case 'popular_wig_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 29], 30, ['sales', 'DESC']);
        break;

      // Get popular lace wigs listings
      case 'popular_lacewig_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 30], 30, ['sales', 'DESC']);
        break;

      // Get popular braid listings
      case 'popular_braid_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 11, 'name' => ['not' => '*kids'], 'id' => ['not' => 126]], 6, ['sales', 'DESC']);
        break;

      // Get new arrival wig listings
      case 'new_wig_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 29], 6);
        break;

      // Get new arrival lace wig listings
      case 'new_lace_wig_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 30], 6);
        break;

      // Get new arrival haircare listings
      case 'new_haircare_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 47, 'name' => ['not' => '*kids']], 6);
        break;

      // Get new arrival jewelry
      case 'new_jewelry_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 62], 6);
        break;

      // Get new arrival misc
      case 'new_misc_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 68], 6);
        break;

      // Get new arrival braid
      case 'new_braid_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 11, 'name' => ['not' => '*kids']], 6);
        break;

      // Get new arrival weave
      case 'new_weave_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 10], 6);
        break;

      // Get new arrival kids itmes
      case 'kid_listings':
        $ret_val = $this->_doHomepageListingQuery(['description' => 'kids'], 6, ['sales', 'DESC']);
        break;

      // Get new arrival extension
      case 'new_extension_listings':
        $ret_val = $this->_doHomepageListingQuery(['category_ids' => 12], 6);
        break;
    }

    return $ret_val;
  }

  /**
   * Perform search
   * @param array $criteria
   * @param int $max_results
   * @param array $sort_by
   * @return Collection
   */
  private function _doHomepageListingQuery(array $criteria, int $max_results = 6, array $sort_by = ['timestamp', 'desc']): Collection
  {
    $default_criteria = ['in_stock' => 'true', 'is_inactive' => 'false'];
    $criteria = array_merge($default_criteria, $criteria);

    return $this->noSQLDataSourceService->findBy('products', $criteria, ['max_results' => $max_results, 'sort_by' => $sort_by])->getResults();
  }
}