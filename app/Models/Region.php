<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'head_name',
    'rw_count',
    'rt_count',
    'kk_count',
    'population',
    'male_count',
    'female_count',
])]
class Region extends Model
{
    protected $table = 'regions';

    protected $primaryKey = 'region_id';

    protected $guarded = [];
}
