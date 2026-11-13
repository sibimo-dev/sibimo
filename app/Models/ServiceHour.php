<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceHour extends Model
{
    protected $fillable = [
        'day_of_week',
        'is_closed',
        'open_time',
        'close_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_closed'   => 'boolean',
    ];
}
