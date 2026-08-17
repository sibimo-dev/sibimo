<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'letter_request_id',
    'letter_type_document_id',
    'file_name',
    'file_path',
])]
class LetterRequestAttachment extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'letter_request_attachments';

    /**
     * Primary key.
     */
    protected $primaryKey = 'attachment_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;

    public function letterRequest()
    {
        return $this->belongsTo(LetterRequest::class, 'letter_request_id', 'letter_request_id');
    }

    public function letterTypeDocument()
    {
        return $this->belongsTo(LetterTypeDocument::class, 'letter_type_document_id', 'letter_type_document_id');
    }
}
