<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'address',
    'category',
    'status',
    'funding_source',
    'budget',
    'volume',
    'volume_unit',
    'executor',
    'year',
    'start_date',
    'target_date',
    'description',
    'latitude',
    'longitude',
    'end_latitude',
    'end_longitude',
    'cover_image',
])]
class Development extends Model
{
    use HasFactory;

    protected $table = 'developments';
    protected $primaryKey = 'development_id';

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'volume' => 'decimal:2',
            'year' => 'integer',
            'start_date' => 'date:Y-m-d',
            'target_date' => 'date:Y-m-d',
            'latitude' => 'float',
            'longitude' => 'float',
            'end_latitude' => 'float',
            'end_longitude' => 'float',
        ];
    }

    public function progress()
    {
        return $this->hasMany(DevelopmentProgress::class, 'development_id', 'development_id')
            ->orderBy('percentage');
    }
}
