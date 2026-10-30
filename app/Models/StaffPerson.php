<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPerson extends Model
{
    use SoftDeletes;

    protected $table = 'staffs';
    protected $guarded = [];

    protected function casts(): array
    {
        return ['birth_date' => 'date:Y-m-d'];
    }

    public function assignments()
    {
        return $this->hasMany(StaffAssignment::class, 'staff_id');
    }

    public function bioPayload(): array
    {
        return [
            'gender' => $this->gender,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'education' => $this->education,
            'major' => $this->major,
            'school_name' => $this->school_name,
            'graduation_year' => $this->graduation_year,
        ];
    }

    // Semua penugasan orang ini (termasuk yang sudah berakhir), terbaru di atas.
    public function historyPayload(): array
    {
        return $this->assignments
            ->sortByDesc(fn ($a) => $a->start_date?->timestamp ?? 0)
            ->values()
            ->map(fn (StaffAssignment $a) => [
                'id' => $a->id,
                'position' => $a->position,
                'region_name' => $a->rt?->number ?? $a->rw?->number,
                'start_date' => $a->start_date?->format('Y-m-d'),
                'end_date' => $a->end_date?->format('Y-m-d'),
                'sk_number' => $a->sk_number,
                'sk_date' => $a->sk_date?->format('Y-m-d'),
                'sk_issuer' => $a->sk_issuer,
                'sk_document' => $a->sk_document,
                'sk_document_name' => $a->sk_document_name,
            ])->all();
    }
}