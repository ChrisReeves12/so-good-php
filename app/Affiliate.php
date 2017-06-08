<?php

namespace App;

/**
 * App\Affiliate
 *
 * @property int $id
 * @property string $name
 * @property string $main_image
 * @property string $website
 * @property string $type
 * @property string $slug
 * @property string $affiliate_tag
 * @property array $social_media_links
 * @property array $images
 * @property bool $is_inactive
 * @property array $videos
 * @property string $tags
 * @property string $short_bio
 * @property string $long_bio
 * @property string $list_page_image
 * @property string $main_image_url
 * @property string $social_media_links_json
 * @property string $list_page_image_url
 * @property string $videos_json
 * @property string $image_prefix
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereMainImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereAffiliateTag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Affiliate whereIsInactive($value)
 * @mixin \Eloquent
 */

class Affiliate extends AbstractRecordType
{
  protected $guarded = [];
  protected $appends = ['main_image_url'];
  protected $casts = ['videos' => 'array', 'social_media_links' => 'array'];

  public function getMainImageUrlAttribute()
  {
    return $this->getImageUrl($this->main_image);
  }

  public function getImagesAttribute()
  {
    return !empty($this->attributes['images']) ? array_values(json_decode($this->attributes['images'], true)) : [];
  }

  public function setImagesAttribute($value)
  {
    $this->attributes['images'] = json_encode(array_values($value));
  }

  public function getVideosJsonAttribute()
  {
    return json_encode($this->videos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  }

  public function getSocialMediaLinksJsonAttribute()
  {
    return json_encode($this->social_media_links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  }

  public function getImagePrefixAttribute()
  {
    return '/content-img/affiliates/' . $this->id . '/';
  }

  public function getImageUrl(string $file)
  {
    return $this->image_prefix . $file;
  }

  public function getListPageImageUrlAttribute()
  {
    return !empty($this->attributes['list_page_image']) ? $this->getImageUrl($this->attributes['list_page_image']) : '';
  }

  public function deleteImageFromDisk(string $file_name)
  {
    if(file_exists(env('APP_ROOT') . '/storage/app/public/content-img/affiliates/' . $this->id . '/' . $file_name))
      unlink(env('APP_ROOT') . '/storage/app/public/content-img/affiliates/' . $this->id . '/' . $file_name);
  }

  public function getViewParameters(): array
  {
    return ['id', 'name', 'slug', 'affiliate_tag', 'website', 'short_bio', 'main_image', 'list_page_image', 'images', 'image_prefix',
      'videos', 'social_media_links', 'main_image', 'is_inactive', 'type', 'videos_json', 'social_media_links_json'];
  }
}
