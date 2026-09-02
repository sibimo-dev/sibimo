<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'request_code',
    'citizen_id',
    'applicant_name',
    'applicant_nik',
    'applicant_phone',
    'applicant_address',
    'letter_type_id',
    'status',
    'signature_type',
    'letter_number',
    'verified_by',
    'authorized_by_signer_id',
    'source',
    'notes',
    'form_data',
    'submitted_at',
    'verified_at',
    'authorized_at',
])]
class LetterRequest extends Model
{
    use HasFactory;

    protected $table = 'letter_requests';
    protected $primaryKey = 'letter_request_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'authorized_at' => 'datetime',
        ];
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id', 'citizen_id');
    }

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id', 'letter_type_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    public function authorizedSigner()
    {
        return $this->belongsTo(Signer::class, 'authorized_by_signer_id', 'staff_id');
    }

    public function attachments()
    {
        return $this->hasMany(LetterRequestAttachment::class, 'letter_request_id', 'letter_request_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(LetterRequestStatusHistory::class, 'letter_request_id', 'letter_request_id');
    }

    protected static function booted(): void
    {
        static::creating(function (LetterRequest $letterRequest) {
            if (empty($letterRequest->request_code)) {
                $prefix = 'REQ-' . now()->format('Ymd');
                $lastNumber = static::where('request_code', 'like', "{$prefix}-%")->count() + 1;
                $letterRequest->request_code = $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
            }
            if (empty($letterRequest->submitted_at)) {
                $letterRequest->submitted_at = now();
            }
            if (empty($letterRequest->status)) {
                $letterRequest->status = 'submitted';
            }
        });
    }
}
