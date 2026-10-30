<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['region_id', 'number', 'head_name', 'head_phone'])]
class Rw extends Model
{
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }
}