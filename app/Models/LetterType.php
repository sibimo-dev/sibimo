<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code',
    'letter_name',
    'category',
    'number_prefix',
    'processing_time',
    'signature_method',
    'signer_id',
    'description',
    'blade_view',
    'is_active',
])]
class LetterType extends Model
{
    use HasFactory;

    protected $table = 'letter_types';
    protected $primaryKey = 'letter_type_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // document_count BUKAN kolom database -- dihitung otomatis dari relasi documents.
    // Kalau controller load pakai ->withCount('documents'), Laravel otomatis
    // isi attribute 'documents_count' -- accessor ini tinggal alias-in ke nama
    // yang dipakai frontend ('document_count', tanpa 's'). Kalau belum di-load
    // pakai withCount, fallback ke query count() manual (lebih lambat).
    protected $appends = ['document_count'];

    public function getDocumentCountAttribute()
    {
        return $this->documents_count ?? $this->documents()->count();
    }

    public function documents()
    {
        return $this->hasMany(LetterTypeDocument::class, 'letter_type_id', 'letter_type_id');
    }

    public function signer()
    {
        return $this->belongsTo(Signer::class, 'signer_id', 'staff_id');
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
