<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
    protected $table = 'gallery_categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = ['name', 'slug'];
}