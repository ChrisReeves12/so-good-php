<?php

namespace App\Http\Controllers\Frontend;

use App\Services\Contracts\IAffiliateService;
use App\Services\Contracts\IBreadcrumbService;
use App\Services\Contracts\IHomePageService;
use App\Services\Contracts\IMailService;
use App\Http\Controllers\Controller;
use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * The HomeController class definition.
 *
 * The main controller of the Frontend section
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/
class HomeController extends Controller
{
  protected $affiliateService;
  protected $mailService;
  protected $breadcrumbService;
  protected $homePageService;

  /**
   * HomeController constructor.
   * @param IAffiliateService $affiliateService
   * @param IMailService $mailService
   * @param IBreadcrumbService $breadcrumbService
   * @param IHomePageService $homePageService
   */
  public function __construct(IAffiliateService $affiliateService, IMailService $mailService,
                              IBreadcrumbService $breadcrumbService, IHomePageService $homePageService)
  {
    $this->affiliateService = $affiliateService;
    $this->mailService = $mailService;
    $this->breadcrumbService = $breadcrumbService;
    $this->homePageService = $homePageService;
  }

  /**
   * Home page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $this->breadcrumbService->add(['<i class="fa fa-home"></i> Home' => '/']);

    return view('frontend.home.index', [
      'new_misc' => $this->homePageService->getHomePageProductListings('new_misc_listings'),
      'kid_items' => $this->homePageService->getHomePageProductListings('kid_listings'),
      'new_weaves' => $this->homePageService->getHomePageProductListings('new_weave_listings'),
      'new_extensions' => $this->homePageService->getHomePageProductListings('new_extension_listings'),
      'new_braids' => $this->homePageService->getHomePageProductListings('new_braid_listings'),
      'new_wigs' => $this->homePageService->getHomePageProductListings('new_wig_listings'),
      'new_lwigs' => $this->homePageService->getHomePageProductListings('new_lace_wig_listings'),
      'new_haircare' => $this->homePageService->getHomePageProductListings('new_haircare_listings'),
      'new_jewelry' => $this->homePageService->getHomePageProductListings('new_jewelry_listings'),
      'popular_braids' => $this->homePageService->getHomePageProductListings('popular_braid_listings'),
      'popular_wigs' => $this->homePageService->getHomePageProductListings('popular_wig_listings'),
      'popular_lacewigs' => $this->homePageService->getHomePageProductListings('popular_lacewig_listings'),
      //'active_vloggers' => $this->affiliateService->getActiveVloggers(current_user('role') == 'admin')
      'active_vloggers' => new Collection()
    ]);
  }

  /**
   * Terms of Service page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function terms()
  {
    $this->breadcrumbService->add(['Terms Of Service' => '/terms']);
    return view('frontend.home.terms', ['page_title' => 'Terms Of Service']);
  }

  /**
   * The about page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function about()
  {
    $this->breadcrumbService->add(['About' => '/about']);
    return view('frontend.home.about', ['page_title' => 'About Us']);
  }

  /**
   * Sweepstakes rules page (temporary)
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function sweepstakes_rules()
  {
    $this->breadcrumbService->add(['Sweepstakes Rules' => '/sweepstakes-rules']);
    return view('frontend.home.sweepstakes_rules', ['page_title' => 'Sweepstakes Rules']);
  }

  /**
   * The contact us page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function contact_us()
  {
    $this->breadcrumbService->add(['Contact Us' => '/contact-us']);
    return view('frontend.home.contact_us', ['page_title' => 'Contact Us']);
  }

  /**
   * The page listing all of the vloggers we are partnered with
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function vloggers()
  {
    $this->breadcrumbService->add(['Beauty Vloggers' => '/beauty-vloggers']);
    return view('frontend.home.vloggers', [
                                              'page_title' => 'Beauty Vloggers',
                                              'vloggers' => $this->affiliateService->getActiveVloggers(
                                                current_user('role') == 'admin')
                                            ]);
  }

  /**
   * Return policy page
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function return_policy()
  {
    $this->breadcrumbService->add(['Return Policy' => '/return-policy']);
    return view('frontend.home.return_policy', ['page_title' => 'Return/Shipping Policy']);
  }

  /**
   * Sends the contact message to email from user
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function do_contact(Request $request)
  {
    // Validate form
    $this->validate($request, [
      'name' => 'required',
      'email' => 'required|email',
      'message' => 'required'
    ]);

    // Send email
    $this->mailService->sendEmail(business('store_email'), new ContactMessage($request->name, $request->get('message'), $request->get('email')));

    $request->session()->flash('flash_success', 'Thanks for your message, we will respond to your inquiry shortly.');
    return redirect('/contact-us');
  }
}