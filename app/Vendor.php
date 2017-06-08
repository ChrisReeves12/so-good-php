<?php

namespace App;

/**
 * App\Vendor
 *
 * @property int $id
 * @property string $name
 * @property bool $is_inactive
 * @property string $description
 * @property string $email
 * @property string $website
 * @property int $address_id
 * @property bool $is_dropshipper
 * @property string $image
 * @property string $phone_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Address $address
 * @property-read mixed $view_image
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereIsDropshipper($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereIsInactive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor wherePhoneNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Vendor whereWebsite($value)
 * @mixin \Eloquent
 */
class Vendor extends AbstractRecordType
{
  protected $guarded = [];

  public function getViewParameters(): array
  {
    return ['id', 'email', 'name', 'website', 'address', 'address_id', 'id', 'view_image', 'is_inactive', 'is_dropshipper', 'phone_number'];
  }

  public function address()
  {
    return $this->belongsTo('App\Address');
  }

  public function getValidationRules($data = []): array
  {
    return [
      'name' => 'required'
    ];
  }

  public function delete()
  {
    $this->address()->dissociate();
    $this->address()->delete();

    // Check if vendor is being used
    if(Product::where('vendor_id', $this->id)->count() > 0)
      throw new \Exception('Cannot delete this vendor because it is being used on products.');

    Vendor::where('id', $this->id)->delete();
  }

  /**
   * Remove image from disk
   * @param string $file_name
   */
  public function deleteImageFromDisk(string $file_name)
  {
    if(file_exists(env('APP_ROOT') . '/storage/app/public/content-img/vendors/' . $this->id . '/' . $file_name))
      unlink(env('APP_ROOT') . '/storage/app/public/content-img/vendors/' . $this->id . '/' . $file_name);
  }

  public function getViewImageAttribute()
  {
    return !empty($this->attributes['image']) ? ['href' => $this->getImageUrl($this->attributes['image']), 'file_name' => $this->attributes['image']] : null;
  }

  public function getImageUrl($file_name)
  {
    return ('/content-img/vendors/' . $this->id . '/' . $file_name);
  }
}
