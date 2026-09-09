<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'record_type',
    'record_event',
    'national_id',
    'family_card_number',
    'dusun',
    'full_name',
    'birth_place',
    'birth_date',
    'age',
    'gender',
    'address',
    'rt',
    'rw',
    'phone_number',
    'birth_certificate_status',
    'birth_certificate_number',
    'blood_type',
    'occupation',
    'education',
    'marital_status',
    'marriage_certificate_status',
    'marriage_certificate_number',
    'marriage_date',
    'divorce_certificate_status',
    'divorce_certificate_number',
    'divorce_date',
    'family_relationship',
    'physical_disability',
    'disability_status',
    'religion',
    'mother_national_id',
    'mother_name',
    'father_national_id',
    'father_name',
    'nationality',
    'ktp_address',
    'status',
])]
#[Hidden([
    'password',
])]
class Citizen extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Table name.
     */
    protected $table = 'citizens';

    /**
     * Primary key.
     */
    protected $primaryKey = 'citizen_id';

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
            'password' => 'hashed',
            'birth_date' => 'date',
            'marriage_date' => 'date',
            'divorce_date' => 'date',
            'age' => 'integer',
        ];
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class, 'citizen_id', 'citizen_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'citizen_id', 'citizen_id');
    }

    public function bookLoans()
    {
        return $this->hasMany(BookLoan::class, 'citizen_id', 'citizen_id');
    }
}
