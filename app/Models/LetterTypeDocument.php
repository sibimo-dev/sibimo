<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'letter_type_id',
    'document_name',
    'description',
    'is_required',
])]
class LetterTypeDocument extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'letter_type_documents';

    /**
     * Primary key.
     */
    protected $primaryKey = 'letter_type_document_id';

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

    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id', 'letter_type_id');
    }

    public function attachments()
    {
        return $this->hasMany(LetterRequestAttachment::class, 'letter_type_document_id', 'letter_type_document_id');
    }
}
