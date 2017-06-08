<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCartDatum extends Model
{
  protected $casts = ['line_items' => 'array'];
}
