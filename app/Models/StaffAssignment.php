<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Region;

class StaffAssignment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'term_end_date' => 'date:Y-m-d',
            'sk_date' => 'date:Y-m-d',
            'is_signer' => 'boolean',
            'position_history' => 'array',
        ];
    }

    public function person()
    {
        return $this->belongsTo(StaffPerson::class, 'staff_id');
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function padukuhan()
    {
        return $this->belongsTo(Region::class, 'padukuhan_id', 'region_id');
    }

    // Aktif = belum berakhir (end_date kosong atau belum lewat).
    public function scopeActive($query)
    {
        return $query->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', today()->toDateString()));
    }

    // Jabatan pamong kalurahan (di luar Ketua RW/RT, yang dikelola lewat wilayah).
    public function scopeManaged($query)
    {
        return $query->whereNotIn('position', ['Ketua RW', 'Ketua RT']);
    }
}
