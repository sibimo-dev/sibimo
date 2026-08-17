<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'section_name',
    'slug',
    'sort_order',
    'is_active',
])]
class ProfileSection extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'profile_sections';

    /**
     * Primary key.
     */
    protected $primaryKey = 'section_id';

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

    public function contents()
    {
        return $this->hasMany(ProfileContent::class, 'section_id', 'section_id');
    }
}
