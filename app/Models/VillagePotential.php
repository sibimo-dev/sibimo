<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category',
    'title',
    'slug',
    'short_desc',
    'description',
    'image',
    'location',
    'contact',
    'extra_info',
    'created_at',
])]
class VillagePotential extends Model
{
    use HasFactory;

    protected $table = 'village_potentials';
    protected $primaryKey = 'potential_id';
    protected $keyType = 'int';
    public $incrementing = true;

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'extra_info' => 'array',
        ];
    }
}