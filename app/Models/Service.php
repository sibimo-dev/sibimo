<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'description',
    'icon',
    'sort_order',
    'is_active',
])]
class Service extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'service';

    /**
     * Primary key.
     */
    protected $primaryKey = 'service_id';

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
            'is_active' => 'boolean',
        ];
    }

}
