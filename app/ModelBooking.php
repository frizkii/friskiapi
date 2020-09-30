<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelBooking extends Model
{
    protected $table = 'TBBooking';
    protected $fillable = [
        'user_id',
        'bicycle_id',
        'date_booking',
        'return_item',
        'status'
    ];
}
