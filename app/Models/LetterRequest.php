<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'citizen_id',
    'letter_type_id',
    'status',
    'form_data',
    'letter_number',
    'signature_type',
    'verified_by',
    'authorized_by',
    'submitted_at',
    'verified_at',
    'authorized_at',
    'completed_at',
    'result_file_path',
    'remarks',
])]
class LetterRequest extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'letter_requests';

    /**
     * Primary key.
     */
    protected $primaryKey = 'letter_request_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;

    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'authorized_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_by', 'user_id');
    }

    public function attachments()
    {
        return $this->hasMany(LetterRequestAttachment::class, 'letter_request_id', 'letter_request_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(LetterRequestStatusHistory::class, 'letter_request_id', 'letter_request_id');
    }
}
