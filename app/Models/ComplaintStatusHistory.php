<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'complaint_id',
    'user_id',
    'status',
    'note',
])]
class ComplaintStatusHistory extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'complaint_status_histories';

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

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id', 'complaint_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
