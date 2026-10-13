<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'book_id',
    'citizen_id',
    'borrowed_at',
    'due_date',
    'returned_at',
    'status',
    'fine_amount',
])]
class BookLoan extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'book_loans';

    /**
     * Primary key.
     */
    protected $primaryKey = 'loan_id';

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
            'borrowed_at' => 'date',
            'due_date' => 'date',
            'returned_at' => 'date',
            'fine_amount' => 'decimal:2',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id', 'citizen_id');
    }
}
