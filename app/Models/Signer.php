<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'position',
])]
class Signer extends Model
{
    use HasFactory;

    protected $table = 'signers';
    protected $primaryKey = 'signer_id';
    protected $keyType = 'int';
    public $incrementing = true;

    public function letterTypes()
    {
        return $this->hasMany(LetterType::class, 'signer_id', 'signer_id');
    }

    public function letterRequestsAuthorized()
    {
        return $this->hasMany(LetterRequest::class, 'authorized_by_signer_id', 'signer_id');
    }
}
