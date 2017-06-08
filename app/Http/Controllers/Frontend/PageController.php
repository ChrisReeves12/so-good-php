<?php
/**
 * The PageController class definition.
 *
 * Handles various pages like product page and the affiliate page
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\IAffiliateService;
use App\Services\Contracts\IBreadcrumbService;
use App\Services\Contracts\ILoggerService;
use App\Services\Contracts\IProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PageController
 * @package App\Http\Controllers\Frontend
 */
class PageController extends Controller
{
  protected $loggerService;
  protected $productService;
  protected $affiliateService;
  protected $breadcrumbService;

  /**
   * PageController constructor.
   * @param ILoggerService $loggerService
   * @param IProductService $productService
   * @param IBreadcrumbService $breadcrumbService
   * @param IAffiliateService $affiliateService
   */
  public function __construct(ILoggerService $loggerService, IProductService $productService, IBreadcrumbService $breadcrumbService, IAffiliateService $affiliateService)
  {
    $this->loggerService = $loggerService;
    $this->productService = $productService;
    $this->breadcrumbService = $breadcrumbService;
    $this->affiliateService = $affiliateService;
  }

  /**
   * Home page for the page
   * @param string $slug
   * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @throws \Exception
   */
  public function index(string $slug)
  {
    $ret_val = [];

    try
    {
      $selected_product = $this->productService->findActiveProduct($slug);
      $selected_affiliate = $this->affiliateService->findActiveAffiliate($slug, (current_user('role') == 'admin'));

      if(!empty($selected_product))
        $page_type = 'product';
      else
        $page_type = 'affiliates';

      if($page_type == 'product' && !empty($selected_product)) // Handle product pages
      {
        $product_detail_data = $this->productService->getProductDataForDetailsPage($selected_product);
        $product_detail_data['recommended_products'] = $this->productService->getRecommendedProducts($selected_product);

        $this->breadcrumbService->add([$product_detail_data['product']['name'] => '/' . $slug]);

        $ret_val = view('frontend.product_page.index', [
          'product_page_data' => $product_detail_data,
          'page_title' => $product_detail_data['product']['name'],
          'selected_product' => $selected_product,
        ]);
      }
      elseif($page_type == 'affiliates' && !empty($selected_affiliate)) // Handle affiliates page
      {
        $this->breadcrumbService->add([$selected_affiliate->name => '/' . $slug]);

        $ret_val = view('frontend.affiliate.index', [
          'selected_affiliate' => $selected_affiliate,
          'page_title' => $selected_affiliate->name,
          'social_network_icons' => [
            'youtube' => 'youtubeicon.png',
            'instagram' => 'instagram.png'
          ]
        ]);
      }

      if(empty($ret_val))
        abort(404);
    }
    catch(NotFoundHttpException $notFoundHttpException)
    {
      throw $notFoundHttpException;
    }
    catch(\Exception $ex)
    {
      $this->loggerService->error("PageController::index: " . $ex->getMessage(), ['slug' => $slug]);

      if(env('APP_ENV') == 'production')
        abort(404);
      else
        throw $ex;
    }

    return $ret_val;
  }

  /**
   * Get updated product data on option change
   * @param Request $request
   * @return array
   */
  public function get_product_data(Request $request)
  {
    $ret_val['system_error'] = false;

    try
    {
      // Find item that matches attributes if product has several
      $item = $this->productService->findItemFromOptions($request->query('product_id'), $request->query('option_values'), $request->query('item_id'));

      if(!empty($item))
      {
        $ret_val = $this->productService->getItemData($item);
      }
      else
      {
        // Item cannot be found
        $ret_val['item']['stock_status'] = 'out_of_stock';
        $ret_val['item']['quantity'] = 0;
      }
    }
    catch(\Exception $ex)
    {
      $ret_val['system_error'] = $ex->getMessage();
    }

    return $ret_val;
  }
}