<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'record_type',
    'record_event',
    'record_year',
    'title_code',
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
    'rt_head_name',
    'rw_head_name',
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
    'disability_type',
    'religion',
    'belief_organization',
    'mother_national_id',
    'mother_name',
    'mother_birth_place',
    'mother_birth_date',
    'mother_occupation',
    'mother_religion',
    'mother_nationality',
    'mother_address',
    'father_national_id',
    'father_name',
    'father_birth_place',
    'father_birth_date',
    'father_occupation',
    'father_religion',
    'father_nationality',
    'father_address',
    'spouse_national_id',
    'spouse_name',
    'spouse_birth_place',
    'spouse_birth_date',
    'spouse_occupation',
    'spouse_religion',
    'spouse_nationality',
    'spouse_address',
    'nationality',
    'ktp_address',
    'previous_address',
    'passport_number',
    'passport_expiry_date',
    'status',
])]
#[Hidden([
    'password',
])]
class Citizen extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (self $citizen): void {
            $birthDate = $citizen->birth_date;

            if (!$birthDate) {
                $citizen->age = null;
                return;
            }

            $birthDate = $birthDate instanceof Carbon
                ? $birthDate
                : Carbon::parse($birthDate);

            $citizen->age = $birthDate->isFuture()
                ? null
                : $birthDate->age;
        });
    }

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
            'passport_expiry_date' => 'date',
            'father_birth_date' => 'date',
            'mother_birth_date' => 'date',
            'spouse_birth_date' => 'date',
            'age' => 'integer',
        ];
    }

    /**
     * Always expose the current age from birth_date, including legacy rows.
     */
    public function getAgeAttribute($value): ?int
    {
        $birthDate = $this->getAttribute('birth_date');

        if (!$birthDate) {
            return null;
        }

        $birthDate = $birthDate instanceof Carbon
            ? $birthDate
            : Carbon::parse($birthDate);

        return $birthDate->isFuture() ? null : $birthDate->age;
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class, 'citizen_id', 'citizen_id');
    }

    public function documents()
    {
        return $this->hasMany(CitizenDocument::class, 'citizen_id', 'citizen_id');
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