<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'complaint_id',
    'file_name',
    'file_path',
])]
class ComplaintAttachment extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'complaint_attachments';

    /**
     * Primary key.
     */
    protected $primaryKey = 'attachment_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = null;

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id', 'complaint_id');
    }
}
