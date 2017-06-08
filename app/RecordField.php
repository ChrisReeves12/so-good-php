<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RecordField
 *
 * @property int $id
 * @property string $name
 * @property string $value_type
 * @property int $record_type_id
 * @property string $formula
 * @property int $sort_order
 * @property bool $searchable
 * @property int $search_priority
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereFormula($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereRecordTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereSearchPriority($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereSearchable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereSortOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecordField whereValueType($value)
 * @mixin \Eloquent
 */
class RecordField extends Model
{
    //
}
