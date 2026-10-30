<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['rw_id', 'number', 'head_name', 'head_phone'])]
class Rt extends Model
{
    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}