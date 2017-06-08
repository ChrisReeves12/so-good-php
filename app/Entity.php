<?php

namespace App;

use Hash;

/**
 * App\Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password_digest
 * @property string $role
 * @property string $ip_address
 * @property bool $is_fraudulent
 * @property int $shipping_address_id
 * @property int $billing_address_id
 * @property string $token
 * @property string $phone_number
 * @property string $status
 * @property bool $is_inactive
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Address $billing_address
 * @property-read mixed $display_record_name
 * @property-read mixed $full_name
 * @property-write mixed $password
 * @property-read \App\Address $shipping_address
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereBillingAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereIsFraudulent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereIsInactive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity wherePasswordDigest($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity wherePhoneNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereRole($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereShippingAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Entity extends AbstractRecordType
{
  protected $guarded = [];
  protected $hidden = ['password_digest'];

  public function getViewParameters(): array
  {
    return ['id', 'first_name', 'last_name', 'email', 'role', 'ip_address', 'is_fraudulent',
      'shipping_address', 'billing_address', 'token', 'phone_number', 'status', 'is_inactive'];
  }

  public function billing_address()
  {
    return $this->belongsTo('App\Address', 'billing_address_id', 'id');
  }

  public function encrypt(string $password)
  {
    return Hash::make($password);
  }

  public function getFullNameAttribute()
  {
    return $this->first_name . ' ' . $this->last_name;
  }

  public function setPasswordAttribute(string $password)
  {
    $this->attributes['password_digest'] = $this->encrypt($password);
  }

  public function getDisplayRecordNameAttribute()
  {
    return $this->full_name;
  }

  public function shipping_address()
  {
    return $this->belongsTo('App\Address', 'shipping_address_id', 'id');
  }

  public function getValidationRules($data = []): array
  {
    $validation_rules = [
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required|email|unique:entities,email' . ($this->exists ? ",{$this->id}" : ''),
      'role' => 'required',
      'status' => 'required'
    ];

    return $validation_rules;
  }
}
