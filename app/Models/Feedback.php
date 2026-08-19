<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'full_name',
    'email',
    'message',
    'status',
    'submitted_at',
])]
class Feedback extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'feedbacks';

    /**
     * Primary key.
     */
    protected $primaryKey = 'feedback_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;

}
