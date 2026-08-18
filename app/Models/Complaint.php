<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'citizen_id',
    'category',
    'title',
    'description',
    'status',
    'submitted_at',
    'resolved_at',
])]
class Complaint extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'complaints';

    /**
     * Primary key.
     */
    protected $primaryKey = 'complaint_id';

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
            'submitted_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id', 'citizen_id');
    }

    public function attachments()
    {
        return $this->hasMany(ComplaintAttachment::class, 'complaint_id', 'complaint_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ComplaintStatusHistory::class, 'complaint_id', 'complaint_id');
    }
}
