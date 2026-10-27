<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillagePotentialCategory extends Model
{
    protected $table = 'village_potential_categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = ['name', 'slug'];
}