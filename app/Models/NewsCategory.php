<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category_name',
    'slug',
])]
class NewsCategory extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'news_categories';

    /**
     * Primary key.
     */
    protected $primaryKey = 'category_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function news()
    {
        return $this->hasMany(News::class, 'category_id', 'category_id');
    }
}
