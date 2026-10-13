<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'village_name',
    'logo',
    'description',
    'social_media',
    'address',
    'phone',
    'email',
    'stamp',
])]
class FooterSetting extends Model
{
    protected $table = 'footer_settings';

    protected $primaryKey = 'footer_setting_id';

    protected function casts(): array
    {
        return [
            'social_media' => 'array',
        ];
    }
}
