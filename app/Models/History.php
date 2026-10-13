<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'year_founded',
    'points',
    'photos',
    'published_by',
    'status',
    'published_at',
])]
class History extends Model
{
    use HasFactory;

    protected $table = 'histories';
    protected $primaryKey = 'history_id';

    protected function casts(): array
    {
        return [
            'points' => 'array',
            'photos' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by', 'user_id');
    }
}
