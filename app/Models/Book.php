<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category_id',
    'title',
    'author',
    'isbn',
    'stock',
])]
class Book extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'books';

    /**
     * Primary key.
     */
    protected $primaryKey = 'book_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;


    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id', 'category_id');
    }

    public function loans()
    {
        return $this->hasMany(BookLoan::class, 'book_id', 'book_id');
    }
}
