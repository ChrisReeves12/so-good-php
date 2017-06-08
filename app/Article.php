<?php

namespace App;

/**
 * App\Article
 *
 * @property int $id
 * @property string $title
 * @property string $summary
 * @property string $body
 * @property bool $is_published
 * @property string $date_published
 * @property int $article_category_id
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\ArticleCategory $article_category
 * @property-read mixed $category_name
 * @property-read mixed $view_body
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereArticleCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereBody($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereDatePublished($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereIsPublished($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereSummary($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Article extends AbstractRecordType
{
  protected $guarded = [];

  public function getViewParameters(): array
  {
    return ['id', 'title', 'summary', 'view_body', 'is_published', 'date_published', 'article_category_id', 'slug'];
  }

  public function getReadOnlyParams(): array
  {
    return ['article_categories', 'view_body'];
  }

  public function getExtraData(): array
  {
    return [
      'article_categories' => ArticleCategory::orderBy('name', 'asc')->get()->map(function(ArticleCategory $ac)
      {
        return [
          'id'    => $ac->id,
          'label' => $ac->name
        ];
      })->toArray()
    ];
  }

  public function article_category()
  {
    return $this->belongsTo('App\ArticleCategory');
  }

  public function getCategoryNameAttribute()
  {
    return $this->article_category->name ?? '';
  }

  public function getViewBodyAttribute()
  {
    return base64_encode($this->body);
  }

  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'title' => 'required',
      'slug'  => 'required|unique:articles,slug' . ($this->exists ? ",{$this->id}" : '') . '|alpha_dash'
    ];

    return $validation_rules;
  }
}
