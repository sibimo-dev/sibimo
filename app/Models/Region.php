<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


#[Fillable([
    'name',
    'head_name',
    'rw_count',
    'rt_count',
    'kk_count',
    'population',
    'male_count',
    'female_count',
    'head_staff_id',
])]
class Region extends Model
{
    protected $table = 'regions';

    protected $primaryKey = 'region_id';

    protected $guarded = [];

    public function headStaff()
    {
        return $this->belongsTo(Staff::class, 'head_staff_id', 'staff_id');
    }

    public function rws()
    {
        return $this->hasMany(Rw::class, 'region_id', 'region_id');
    }

    // Kalau Kepala Dusun sudah ditunjuk dari tabel staff, nama yang tampil mengikuti staff.
    // Kalau belum, tetap memakai teks lama di kolom head_name.
    // Dengan begini halaman Data Wilayah dan halaman profil tidak perlu diubah.
    protected function headName(): Attribute
    {
        return Attribute::get(fn (?string $value) => $this->headStaff?->name ?? $value);
    }

    public function rts()
    {
        return $this->hasManyThrough(Rt::class, Rw::class, 'region_id', 'rw_id', 'region_id', 'id');
    }
}