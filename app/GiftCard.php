<?php

namespace App;


class GiftCard extends AbstractRecordType
{
  protected $guarded = [];

  /**
   * Get validation rules
   * @param array $data
   * @return array
   */
  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'number'   => 'required|unique:gift_cards,number' . ($this->exists ? ",{$this->id}" : '') . '|digits:10',
      'balance'     => 'required|numeric'
    ];

    return $validation_rules;
  }

  public function getExpDateAttribute()
  {
    return !empty($this->attributes['exp_date']) ? (new \Datetime($this->attributes['exp_date']))->format('m/d/Y') : '';
  }
}
