<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'slug', 'category', 'status', 'address', 'description',
    'budget', 'volume', 'volume_unit', 'funding_source', 'executor',
    'year', 'start_date', 'target_date',
    'latitude', 'longitude', 'end_latitude', 'end_longitude',
    'cover_image', 'photo_0', 'photo_50', 'photo_100', 'created_by',
])]
class Development extends Model
{
    use HasFactory;

    protected $table = 'developments';
    protected $primaryKey = 'development_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'start_date'    => 'date',
            'target_date'   => 'date',
            'budget'        => 'float',
            'volume'        => 'float',
            'latitude'      => 'float',
            'longitude'     => 'float',
            'end_latitude'  => 'float',
            'end_longitude' => 'float',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}