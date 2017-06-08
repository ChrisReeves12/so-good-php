<?php

namespace App;

use DateTimeZone;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Log
 *
 * @property int $id
 * @property string $type
 * @property string $message
 * @property string $extra_data
 * @property string $code
 * @property int $line
 * @property string $stack_trace
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $formatted_date
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereExtraData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereLine($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereStackTrace($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Log extends Model
{
    protected $guarded = [];
    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
      return $this->created_at->setTimezone(new DateTimeZone(business('timezone')))->format('F d, Y') . ' at ' .
        $this->created_at->setTimezone(new DateTimeZone(business('timezone')))->format('g:i a');
    }
}
