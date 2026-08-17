<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category_id',
    'author_id',
    'title',
    'slug',
    'content',
    'thumbnail',
    'status',
    'published_at',
])]
class News extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'news';

    /**
     * Primary key.
     */
    protected $primaryKey = 'news_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;


    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id', 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }
}
