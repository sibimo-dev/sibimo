<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'vision',
    'missions',
    'published_by',
    'status',
    'published_at',
])]
class VisionMission extends Model
{
    use HasFactory;

    protected $table = 'vision_missions';
    protected $primaryKey = 'vision_mission_id';

    protected function casts(): array
    {
        return [
            'missions' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by', 'user_id');
    }
}
