<?php

namespace App;

/**
 * App\ArticleCategory
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleCategory whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleCategory whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleCategory whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleCategory extends AbstractRecordType
{
  public function getViewParameters(): array
  {
    return ['id', 'name', 'description', 'slug'];
  }

  public function getValidationRules($data = []): array
  {
    return [
      'name'  => 'required',
      'slug'  => 'required|unique:article_categories,slug' . ($this->exists ? ",{$this->id}" : ''),
    ];
  }
}
