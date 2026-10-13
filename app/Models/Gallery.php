<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'category',
    'location',
    'event_date',
    'description',
    'image',
    'uploaded_by',
    'uploaded_at',
])]
class Gallery extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'galleries';

    /**
     * Primary key.
     */
    protected $primaryKey = 'gallery_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'user_id');
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

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'event_date' => 'date',
        ];
    }
}
