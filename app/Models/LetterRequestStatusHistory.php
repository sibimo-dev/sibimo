<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'letter_request_id',
    'status',
    'note',
    'change_by',
])]
class LetterRequestStatusHistory extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'letter_request_status_histories';

    /**
     * Primary key.
     */
    protected $primaryKey = 'history_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;

    public function letterRequest()
    {
        return $this->belongsTo(LetterRequest::class, 'letter_request_id', 'letter_request_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'change_by', 'user_id');
    }
}
