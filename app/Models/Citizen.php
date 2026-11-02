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
    'national_id',
    'family_card_number',
    'full_name',
    'birth_place',
    'birth_date',
    'age',
    'gender',
    'address',
    'rt_id',          // FK utama ke rts.id
    'rt',             // nomor RT teks (backward-compat)
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
    'disability_type',
    'disability_status',
    'religion',
    'belief_organization',
    'nationality',
    'mother_national_id',
    'mother_name',
    'father_national_id',
    'father_name',
    'spouse_national_id',
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

            // Sinkronisasi kolom rt teks dari relasi rt_id bila rt_id diisi
            // tetapi rt string belum diisi.
            if ($citizen->rt_id && !$citizen->rt) {
                $rtModel = Rt::find($citizen->rt_id);
                if ($rtModel) {
                    $citizen->rt = $rtModel->number;
                }
            }
        });
    }

    protected $table = 'citizens';
    protected $primaryKey = 'citizen_id';
    protected $keyType = 'int';
    public $incrementing = true;

    /* ------------------------------------------------------------------ */
    /* Relasi wilayah: rt_id → rts → rws → regions (padukuhan)            */
    /* ------------------------------------------------------------------ */

    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function rtArea()
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function getRtModel(): ?Rt
    {
        if ($this->relationLoaded('rtArea')) {
            $rel = $this->getRelation('rtArea');
            if ($rel instanceof Rt) return $rel;
        }
        if ($this->relationLoaded('rt')) {
            $rel = $this->getRelation('rt');
            if ($rel instanceof Rt) return $rel;
        }

        return $this->rt_id ? Rt::with('rw.region')->find($this->rt_id) : null;
    }

    /* ------------------------------------------------------------------ */
    /* Relasi keluarga via NIK                                              */
    /* ------------------------------------------------------------------ */

    public function mother()
    {
        return $this->belongsTo(self::class, 'mother_national_id', 'national_id');
    }

    public function father()
    {
        return $this->belongsTo(self::class, 'father_national_id', 'national_id');
    }

    public function spouse()
    {
        return $this->belongsTo(self::class, 'spouse_national_id', 'national_id');
    }

    /* ------------------------------------------------------------------ */
    /* Accessor — nama orang tua & pasangan dari relasi NIK                */
    /* Menjaga kompatibilitas persuratan & kode lama tanpa kolom fisik.    */
    /* ------------------------------------------------------------------ */

    protected $appends = [
        'dusun',
        'rw',
        'father_name',
        'mother_name',
        'spouse_name',
    ];

    public function getFatherNameAttribute(): ?string
    {
        return $this->father?->full_name 
            ?? (!empty($this->attributes['father_name']) ? $this->attributes['father_name'] : null);
    }

    public function getFatherBirthPlaceAttribute(): ?string
    {
        return $this->father?->birth_place;
    }

    public function getFatherBirthDateAttribute(): mixed
    {
        return $this->father?->birth_date;
    }

    public function getFatherOccupationAttribute(): ?string
    {
        return $this->father?->occupation;
    }

    public function getFatherReligionAttribute(): ?string
    {
        return $this->father?->religion;
    }

    public function getFatherNationalityAttribute(): ?string
    {
        return $this->father?->nationality;
    }

    public function getFatherAddressAttribute(): ?string
    {
        return $this->father?->ktp_address ?? $this->father?->address;
    }

    public function getMotherNameAttribute(): ?string
    {
        return $this->mother?->full_name 
            ?? (!empty($this->attributes['mother_name']) ? $this->attributes['mother_name'] : null);
    }

    public function getMotherBirthPlaceAttribute(): ?string
    {
        return $this->mother?->birth_place;
    }

    public function getMotherBirthDateAttribute(): mixed
    {
        return $this->mother?->birth_date;
    }

    public function getMotherOccupationAttribute(): ?string
    {
        return $this->mother?->occupation;
    }

    public function getMotherReligionAttribute(): ?string
    {
        return $this->mother?->religion;
    }

    public function getMotherNationalityAttribute(): ?string
    {
        return $this->mother?->nationality;
    }

    public function getMotherAddressAttribute(): ?string
    {
        return $this->mother?->ktp_address ?? $this->mother?->address;
    }

    public function getSpouseNameAttribute(): ?string
    {
        return $this->spouse?->full_name;
    }

    public function getSpouseBirthPlaceAttribute(): ?string
    {
        return $this->spouse?->birth_place;
    }

    public function getSpouseBirthDateAttribute(): mixed
    {
        return $this->spouse?->birth_date;
    }

    public function getSpouseOccupationAttribute(): ?string
    {
        return $this->spouse?->occupation;
    }

    public function getSpouseReligionAttribute(): ?string
    {
        return $this->spouse?->religion;
    }

    public function getSpouseNationalityAttribute(): ?string
    {
        return $this->spouse?->nationality;
    }

    public function getSpouseAddressAttribute(): ?string
    {
        return $this->spouse?->ktp_address ?? $this->spouse?->address;
    }

    /* ------------------------------------------------------------------ */
    /* Accessor wilayah — derive dari rt_id jika string lama kosong        */
    /* ------------------------------------------------------------------ */

    public function getRtAttribute($value = null): ?string
    {
        if ($value !== null && $value !== '') return (string) $value;
        return $this->getRtModel()?->number;
    }

    public function getRwAttribute($value = null): ?string
    {
        if ($value !== null && $value !== '') return (string) $value;
        return $this->getRtModel()?->rw?->number;
    }

    public function getDusunAttribute($value = null): ?string
    {
        if ($value !== null && $value !== '') return (string) $value;
        return $this->getRtModel()?->rw?->region?->name;
    }

    /* ------------------------------------------------------------------ */
    /* Casts                                                                */
    /* ------------------------------------------------------------------ */

    protected function casts(): array
    {
        return [
            'password'            => 'hashed',
            'birth_date'          => 'date',
            'marriage_date'       => 'date',
            'divorce_date'        => 'date',
            'passport_expiry_date'=> 'date',
            'age'                 => 'integer',
        ];
    }

    /**
     * Always expose the current age from birth_date, including legacy rows.
     */
    public function getAgeAttribute($value): ?int
    {
        $birthDate = $this->getAttributes()['birth_date'] ?? null;

        if (!$birthDate) {
            return null;
        }

        $birthDate = $birthDate instanceof Carbon
            ? $birthDate
            : Carbon::parse($birthDate);

        return $birthDate->isFuture() ? null : $birthDate->age;
    }

    /* ------------------------------------------------------------------ */
    /* Relasi lainnya                                                       */
    /* ------------------------------------------------------------------ */

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
