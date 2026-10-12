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

<<<<<<< Updated upstream
    public function creator()
=======
    public function getCoverImageAttribute($value): ?string
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($value);
        return str_starts_with($url, '/')
            ? rtrim((string) config('app.url'), '/') . $url
            : $url;
    }

    public function progress()
>>>>>>> Stashed changes
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}