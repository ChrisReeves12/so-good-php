<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RecordType
 *
 * @property int $id
 * @property string $name
 * @property string $model
 * @property string $formal_name
 * @property string $edit_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RecordField[] $record_fields
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereEditUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereFormalName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereModel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RecordType extends Model
{
    public function record_fields()
    {
      return $this->hasMany('App\RecordField');
    }
}
