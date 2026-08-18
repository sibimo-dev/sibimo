<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserPermission extends Model
{
    protected $table = 'user_permissions';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'permission_id',
    ];

    public function permissions(): BelongsTo
    {
        return $this->belongsTo(
            Permission::class,
            'permission_id',
            'permission_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }
}
