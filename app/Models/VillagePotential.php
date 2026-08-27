<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category',
    'title',
    'description',
    'image',
    'location',
    'created_at',
])]
class VillagePotential extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'village_potentials';

    /**
     * Primary key.
     */
    protected $primaryKey = 'potential_id';

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

}
