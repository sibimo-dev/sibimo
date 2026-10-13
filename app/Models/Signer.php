<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'position',
    'signature_image',
])]
class Signer extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $appends = ['signer_id'];

    protected static function booted(): void
    {
        static::creating(fn (self $signer) => $signer->is_signer = true);
        static::addGlobalScope('signers', fn ($query) => $query->where('is_signer', true));
    }

    public function letterTypes()
    {
        return $this->hasMany(LetterType::class, 'signer_id', 'staff_id');
    }

    public function letterRequestsAuthorized()
    {
        return $this->hasMany(LetterRequest::class, 'authorized_by_signer_id', 'staff_id');
    }

    public function getSignerIdAttribute(): int
    {
        return (int) $this->getKey();
    }
}
