<?php
/**
 * The ProductListingController class definition.
 *
 * Handles product listings
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\IBreadcrumbService;
use App\Services\Contracts\IProductService;
use App\Services\Contracts\ILoggerService;
use App\Services\Contracts\IProductCategoryService;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use Illuminate\Http\Request;

/**
 * Class ProductListingController
 * @package App\Http\Controllers\Frontend
 */
class ProductListingController extends Controller
{
  protected $allowed_types = ['site-search', 'category'];

  protected $allowed_sort_types = ['newest', 'price_asc', 'price_desc', 'relevance'];

  protected $loggerService;
  protected $productCategoryService;
  protected $productService;
  protected $breadcrumbService;

  /**
   * ProductListingController constructor.
   * @param ILoggerService $logger
   * @param IProductCategoryService $productCategoryService
   * @param IProductService $productService
   * @param IBreadcrumbService $breadcrumbService
   */
  public function __construct(ILoggerService $logger, IProductCategoryService $productCategoryService, IProductService $productService, IBreadcrumbService $breadcrumbService)
  {
    $this->loggerService = $logger;
    $this->productCategoryService = $productCategoryService;
    $this->productService = $productService;
    $this->breadcrumbService = $breadcrumbService;
  }

  /**
   * Shows the listing for the product categories or search
   * @param Request $request
   * @param string $type
   * @param string $slug
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @throws \Exception
   */
  public function index(Request $request, string $type = 'site-search', string $slug = null)
  {
    $ret_val = [];

    try
    {
      if(!in_array($type, $this->allowed_types))
      {
        abort(404);
      }

      $price_filter = $request->query('price_filter') ?? 'all';

      $page = (empty($request->query('page')) || !is_numeric($request->query('page')) ||
        ($request->query('page') < 1)) ? 1 : $request->query('page');

      // Handle search
      if($type == 'site-search')
      {
        $page_title = 'Search Results';
        $sort_by = (empty($request->query('sort_by')) || !in_array($request->query('sort_by'), $this->allowed_sort_types)) ?
          business('default_search_sort_method') : $request->query('sort_by');

        $result_set = $this->productService->getProductListings('search', $request->query('keyword'), $sort_by, $price_filter, $page);
      }
      else // Category product search
      {
        /** @var ProductCategory $product_category */
        $product_category = $this->productCategoryService->findBySlug($slug);

        if(!($product_category instanceof ProductCategory))
        {
          abort(404);
        }

        $page_title = $product_category->name;
        $sub_categories = $this->productCategoryService->getFilteredSubCategoriesForListing($product_category);

        $sort_by = (empty($request->query('sort_by')) || !in_array($request->query('sort_by'), $this->allowed_sort_types)) ?
          business('default_category_listing_method') : $request->query('sort_by');

        $result_set = $this->productService->getProductListings('category', $product_category->id, $sort_by, $price_filter, $page);
      }

      $this->breadcrumbService->add([(($type == 'site-search') ? "<i class='fa fa-search'></i> " : "") . $page_title => $request->getRequestUri()]);

      // Check if we are rendering to json or not
      if($request->query('is_json'))
      {
        $ret_val = [
          'products' => $result_set['listings'],
          'num_of_pages' => ceil($result_set['num_of_listings'] / business('products_per_page')),
          'num_of_listings' => $result_set['num_of_listings']
        ];
      }
      else
      {
        $ret_val = view('frontend.product_listing.index', [
          'page_title'   => $page_title,
          'listing_data' => [
            'banner'          => (!($type == 'site-search') && !empty($product_category) && !empty($product_category->banner)) ? $product_category->getImageUrl($product_category->banner) : null,
            'slug'            => $slug,
            'title'           => $page_title,
            'products'        => $result_set['listings'],
            'price_filter'    => $price_filter,
            'brand_facets'    => $result_set['brand_facets']->toArray(),
            'page'            => $page ?? 1,
            'sort_by'         => $sort_by,
            'keyword'         => $request->query('keyword'),
            'num_of_pages'    => ceil($result_set['num_of_listings']/ business('products_per_page')),
            'num_of_listings' => $result_set['num_of_listings'],
            'sub_categories'  => empty($sub_categories) ? [] : $sub_categories->toArray(),
            'list_type'       => $type
          ]]);
      }

    }
    catch(\Exception $ex)
    {
      $this->loggerService->error('ProductListingController::index: ' . $ex->getMessage(), ['slug' => $slug, 'type' => $type, 'request' => $request->all()]);

      if(env('APP_ENV') == 'production')
      {
        abort(404);
      }
      else
      {
        throw $ex;
      }
    }

    return $ret_val;
  }
}