<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category_name',
    'description',
])]
class BookCategory extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'book_categories';

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


    public function books()
    {
        return $this->hasMany(Book::class, 'category_id', 'category_id');
    }
}
