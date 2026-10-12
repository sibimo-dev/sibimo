<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'category',
    'status',
    'number',
    'year',
    'description',
    'document',
])]
class LegalProduct extends Model
{
    use HasFactory;

    protected $table = 'legal_products';
    protected $primaryKey = 'legal_product_id';
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'category', 'status', 'number', 'year',
    'description', 'document', 'created_by',
])]
class LegalProduct extends Model
{
    protected $table = 'legal_products';
    protected $primaryKey = 'legal_product_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected function casts(): array
    {
        return ['year' => 'integer'];
    }
}
}
