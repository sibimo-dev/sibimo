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

    public function getImageAttribute($value): ?string
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

    public function development()
    {
        return $this->belongsTo(Development::class, 'development_id', 'development_id');
    }
}
