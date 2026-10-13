<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'letter_type_id',
    'year',
    'last_sequence',
])]
class LetterNumberSequence extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'letter_number_sequences';

    /**
     * Primary key.
     */
    protected $primaryKey = 'sequence_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id', 'letter_type_id');
    }
}
