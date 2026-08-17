<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code',
    'letter_name',
    'description',
    'blade_view',
    'number_prefix',
    'is_active',
])]
class LetterType extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'letter_types';

    /**
     * Primary key.
     */
    protected $primaryKey = 'letter_type_id';

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
            'is_active' => 'boolean',
        ];
    }

    public function documents()
    {
        return $this->hasMany(LetterTypeDocument::class, 'letter_type_id', 'letter_type_id');
    }

    public function numberSequences()
    {
        return $this->hasMany(LetterNumberSequence::class, 'letter_type_id', 'letter_type_id');
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class, 'letter_type_id', 'letter_type_id');
    }
}
