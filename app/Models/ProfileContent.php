<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'section_id',
    'title',
    'content',
    'thumbnail',
    'published_by',
    'status',
    'published_at',
])]
class ProfileContent extends Model
{
    use HasFactory;

    /**
     * Table name.
     */
    protected $table = 'profile_contents';

    /**
     * Primary key.
     */
    protected $primaryKey = 'profile_content_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;


    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function section()
    {
        return $this->belongsTo(ProfileSection::class, 'section_id', 'section_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by', 'user_id');
    }
}
