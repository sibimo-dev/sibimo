<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'description',
    'event_date',
    'start_time',
    'end_time',
    'location',
    'craated_by',
])]
class Agenda extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'agendas';

    /**
     * Primary key.
     */
    protected $primaryKey = 'agenda_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
