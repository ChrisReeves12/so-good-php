<?php
/**
 * The ArticleServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Article;
use App\ArticleCategory;
use App\Services\Contracts\IArticleService;
use Illuminate\Support\Collection;

/**
 * Class ArticleServiceImpl
 * @package App\Services
 */
class ArticleServiceImpl implements IArticleService
{
  /**
   * Find an article by the slug
   * @param string $slug
   * @return Article
   */
  public function findArticleBySlug(string $slug)
  {
    return Article::whereRaw('lower(slug) = lower(?) AND is_published = true', [$slug])->first();
  }

  /**
   * Find sibling articles of the given article
   * @param Article $article
   * @return Collection
   */
  public function findSiblingArticles(Article $article): Collection
  {
    return Article::whereRaw('id != ? AND article_category_id = ? AND is_published = true', [
      $article->id, $article->article_category_id
    ])->get();
  }

  /**
   * Create a fake article to be used for article previewing
   * @param array $article_data
   * @return Article
   */
  public function createArticlePreview(array $article_data): Article
  {
    return new Article([
      'title' => $article_data['title'],
      'summary' => $article_data['summary'],
      'body' => $article_data['body'],
      'article_category_id' => $article_data['article_category_id'],
      'slug' => 'preview-article-' . time(),
      'created_at' => new \DateTime()
    ]);
  }

  /**
   * Find article category by slug
   * @param string $slug
   * @return ArticleCategory
   */
  public function findArticleCategoryBySlug(string $slug)
  {
    return ArticleCategory::whereRaw('lower(slug) = lower(?)', [$slug])->first();
  }

  /**
   * Return page data on article category
   * @param ArticleCategory $article_category
   * @param int $per_page_count
   * @param int $current_page
   * @return array
   */
  public function getArticleCategoryPageData(ArticleCategory $article_category, $per_page_count, $current_page)
  {
    $article_count = Article::where('article_category_id', $article_category->id)->count();
    $per_page_count = ($per_page_count > $article_count) ? $article_count : $per_page_count;
    $page_count = ceil($article_count / $per_page_count);

    $page = $current_page ?? 1;
    $page = (intval($page) < 0) ? 1 : intval($page);
    $page = ($page > $page_count) ? $page_count : $page;

    return [$article_count, $page_count, $page];
  }

  /**
   * Get articles category
   * @param ArticleCategory $article_category
   * @param int $page
   * @param int $per_page_count
   * @return Collection
   */
  public function getArticlesFromArticleCategory(ArticleCategory $article_category, $page, $per_page_count)
  {
    return Article::whereRaw('article_category_id = ? AND is_published = ?', [$article_category->id, true])
      ->orderBy('created_at', 'DESC')
      ->offset(($page - 1) * $per_page_count)
      ->take($per_page_count)
      ->get();
  }
}