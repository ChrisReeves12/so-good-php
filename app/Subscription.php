<?php

namespace App;

use App\Services\Contracts\IMailListService;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Subscription
 *
 * @property int $id
 * @property string $email
 * @property bool $is_inactive
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property bool $synced
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereIsInactive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereSynced($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscription extends AbstractRecordType
{
  protected $guarded = [];

  public function delete()
  {
    /** @var IMailListService $mailListService */
    $mailListService = app()->make(IMailListService::class);
    $mailListService->removeContact($this->email, 'web');

    parent::delete();
  }

  /**
   * Get validation rules
   * @param array $data
   * @return array
   */
  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'email' => 'required|email|unique:entities,email' . ($this->exists ? ",{$this->id}" : ''),
    ];

    return $validation_rules;
  }

  public function getDateAddedAttribute()
  {
    return human_time($this->created_at);
  }
}
