<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Popup
 *
 * @property int $id
 * @property string $name
 * @property string $internal_name
 * @property string $cookie_name
 * @property string $body
 * @property int $width
 * @property int $height
 * @property string $success_body
 * @property array $close_button_css
 * @property array $window_options
 * @property array $exclude_urls
 * @property array $exclude_pages
 * @property array $server_actions
 * @property bool $exclude_newsletter_subs
 * @property bool $exclude_regged_ursers
 * @property int $cookie_day_life
 * @property bool $is_inactive
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class Popup extends AbstractRecordType
{
  protected $guarded = [];
  protected $casts = [
    'exclude_urls' => 'array',
    'exclude_pages' => 'array',
    'close_button_css' => 'array',
    'window_options' => 'array',
    'server_actions' => 'array'
  ];

  public function canBeDuplicated()
  {
    return true;
  }

  public function getViewParameters(): array
  {
    return $this->getColumnNames();
  }

  public function getReadOnlyParams(): array
  {
    return ['created_at', 'updated_at'];
  }

  /**
   * Get validation rules
   * @param array $data
   * @return array
   */
  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'name'            => 'required',
      'height'          => 'required|numeric',
      'width'           => 'required|numeric',
      'internal_name'   => 'required|unique:popups,internal_name' . ($this->exists ? ",{$this->id}" : '') . '|alpha_dash',
      'cookie_name'     => 'required|unique:popups,cookie_name' . ($this->exists ? ",{$this->id}" : '') . '|alpha_dash',
      'cookie_day_life' => 'numeric_if_exists'
    ];

    return $validation_rules;
  }
}
