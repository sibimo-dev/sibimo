<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'citizen_id',
    'document_type',
    'file_name',
    'storage_path',
    'mime_type',
    'file_size',
    'file_hash',
    'is_active',
    'last_used_at',
])]
class CitizenDocument extends Model
{
    use HasFactory;

    protected $table = 'citizen_documents';
    protected $primaryKey = 'citizen_document_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id', 'citizen_id');
    }

    public function attachments()
    {
        return $this->hasMany(LetterRequestAttachment::class, 'citizen_document_id', 'citizen_document_id');
    }
}
