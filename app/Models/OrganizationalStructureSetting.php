<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationalStructureSetting extends Model
{
    protected $fillable = ['show_former_officials', 'former_officials'];

    protected $casts = [
        'show_former_officials' => 'boolean',
        'former_officials' => 'array',
    ];
}