<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'letter_type_id',
    'field_label',
    'field_key',
    'field_type',
    'is_required',
    'options',
    'sort_order',
    'created_at',
])]
class LetterTypeField extends Model
{
    protected $table = 'letter_type_fields';
    protected $primaryKey = 'field_id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
        ];
    }

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id', 'letter_type_id');
    }
}