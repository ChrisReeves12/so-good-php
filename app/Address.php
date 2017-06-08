<?php

namespace App;

/**
 * App\Address
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $line_1
 * @property string $line_2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $phone_number
 * @property string $company
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereCompany($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereLine1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereLine2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address wherePhoneNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Address whereZip($value)
 * @mixin \Eloquent
 */
class Address extends AbstractRecordType
{
  protected $guarded = ['same_as_billing', 'id'];

  public function getViewParameters(): array
  {
    return ['id', 'company', 'first_name', 'last_name', 'line_1', 'line_2', 'city', 'state', 'zip', 'country', 'phone_number'];
  }

  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'line_1' => 'required',
      'city' => 'required',
      'state' => 'required|alpha_dash',
      'zip' => 'required'
    ];

    return $validation_rules;
  }
}
