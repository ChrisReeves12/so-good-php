<?php
/**
 * The ArticleController class definition.
 *
 * Handle displaying of articles and article categories
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Http\Controllers\Frontend;

use App\Article;
use App\ArticleCategory;
use App\Services\Contracts\IArticleService;
use App\Services\Contracts\IBreadcrumbService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;

/**
 * Class ArticleController
 * @package App\Http\Controllers\Frontend
 */
class ArticleController extends Controller
{
  protected $articleService;
  protected $breadcrumbService;

  /**
   * ArticleController constructor.
   * @param IArticleService $articleService
   * @param IBreadcrumbService $breadcrumbService
   */
  public function __construct(IArticleService $articleService, IBreadcrumbService $breadcrumbService)
  {
    $this->articleService = $articleService;
    $this->breadcrumbService = $breadcrumbService;
  }

  /**
   * @param string $slug
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index($slug)
  {
    // Locate article
    $article = $this->articleService->findArticleBySlug($slug);
    if(!($article instanceof Article))
      abort(404);

    $other_articles = $this->articleService->findSiblingArticles($article);
    $this->breadcrumbService->add([$article->title => '/article/' . $article->slug]);
    return view('frontend.article.index', compact('article', 'other_articles'));
  }

  /**
   * The preview page where you can see an article preview from the Admin panel
   * @param Request $request
   * @return array
   */
  public function preview(Request $request)
  {
    $ret_val = ['errors' => false, 'system_error' => false];

    // Create a fake article to display
    $article = $this->articleService->createArticlePreview($request->get('data'));
    $ret_val['output'] = View::make('frontend.article.index', compact('article'))->render();

    return $ret_val;
  }

  /**
   * Shows a listing of all the articles in an article category
   * @param string $slug
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function article_category($slug, Request $request)
  {
    $per_page_count = 20;

    $article_category = $this->articleService->findArticleCategoryBySlug($slug);
    $this->breadcrumbService->add([$article_category->name => '/article-category/' . $article_category->slug]);
    if(!($article_category instanceof ArticleCategory))
      abort(404);

    list($article_count, $page_count, $page) = $this->articleService->getArticleCategoryPageData($article_category, $per_page_count, $request->get('page'));
    $articles = $this->articleService->getArticlesFromArticleCategory($article_category, $page, $per_page_count);
    $page_title = $article_category->name;

    // Locate article category
    return view('frontend.article.article_category', compact('article_category', 'articles', 'article_count', 'page_count', 'page', 'page_title'));
  }
}