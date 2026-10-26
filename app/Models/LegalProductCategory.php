<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_name', 'slug', 'description'])]
class LegalProductCategory extends Model
{
    protected $table = 'legal_product_categories';
    protected $primaryKey = 'category_id';

    public function legalProducts()
    {
        return $this->hasMany(LegalProduct::class, 'category', 'slug');
    }
}