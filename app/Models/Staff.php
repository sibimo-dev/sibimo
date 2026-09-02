<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'position',
    'level',
    'description',
    'photo',
    'is_signer',
])]
class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staff_id';

    protected function casts(): array
    {
        return ['is_signer' => 'boolean'];
    }

    public function letterTypes()
    {
        return $this->hasMany(LetterType::class, 'signer_id', 'staff_id');
    }

    public function letterRequestsAuthorized()
    {
        return $this->hasMany(LetterRequest::class, 'authorized_by_signer_id', 'staff_id');
    }
}
