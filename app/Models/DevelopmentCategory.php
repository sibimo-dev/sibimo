<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_name', 'slug'])]
class DevelopmentCategory extends Model
{
    protected $table = 'development_categories';
    protected $primaryKey = 'category_id';

    public function developments()
    {
        return $this->hasMany(Development::class, 'category', 'slug');
    }
}