<?php
/**
 * The IArticleService interface definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;

use App\Article;
use App\ArticleCategory;
use Illuminate\Support\Collection;

/**
 * Interface IArticleService
 * @package App\Services\Contracts
 */
interface IArticleService
{
  /**
   * Find an article by the slug
   * @param string $slug
   * @return Article
   */
  public function findArticleBySlug(string $slug);

  /**
   * Find sibling articles of the given article
   * @param Article $article
   * @return Collection
   */
  public function findSiblingArticles(Article $article): Collection;

  /**
   * Create a fake article to be used for article previewing
   * @param array $article_data
   * @return Article
   */
  public function createArticlePreview(array $article_data): Article;

  /**
   * Find article category by slug
   * @param string $slug
   * @return ArticleCategory
   */
  public function findArticleCategoryBySlug(string $slug);

  /**
   * Return page data on article category ($article_count, $page_count, $page)
   * @param ArticleCategory $article_category
   * @param int $per_page_count
   * @param int $current_page
   * @return array
   */
  public function getArticleCategoryPageData(ArticleCategory $article_category, $per_page_count, $current_page);

  /**
   * Get articles category
   * @param ArticleCategory $article_category
   * @param int $page
   * @param int $per_page_count
   * @return Collection
   */
  public function getArticlesFromArticleCategory(ArticleCategory $article_category, $page, $per_page_count);
}