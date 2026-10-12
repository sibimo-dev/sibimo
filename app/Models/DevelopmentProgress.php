<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'development_id',
    'percentage',
    'image',
])]
class DevelopmentProgress extends Model
{
    protected $table = 'development_progress';
    protected $primaryKey = 'progress_id';

    protected function casts(): array
    {
        return ['percentage' => 'integer'];
    }

    public function development()
    {
        return $this->belongsTo(Development::class, 'development_id', 'development_id');
    }
}
