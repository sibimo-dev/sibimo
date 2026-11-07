<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\Citizen;
use App\Models\StaffAssignment;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function index()
    {
        // Hitung statistik kependudukan dari data warga aktif.
        // Jalur utama: citizens.rt_id -> rts -> rws -> regions.
        // Jalur fallback: data lama/import yang belum memiliki rt_id tetapi
        // alamatnya berisi nama padukuhan yang sama dengan tabel regions.
        $stats = Citizen::query()
            ->leftJoin('rts as citizen_rt', 'citizens.rt_id', '=', 'citizen_rt.id')
            ->leftJoin('rws as citizen_rw', 'citizen_rt.rw_id', '=', 'citizen_rw.id')
            ->leftJoin('regions as relation_region', 'citizen_rw.region_id', '=', 'relation_region.region_id')
            ->leftJoin('regions as legacy_region', function ($join): void {
                $join->whereRaw('LOWER(TRIM(legacy_region.name)) = LOWER(TRIM(citizens.address))');
            })
            ->whereRaw("UPPER(citizens.record_type) = 'PENDUDUK'")
            ->where('citizens.status', 'Active')
            ->where(function ($query): void {
                $query->whereNotNull('relation_region.region_id')
                    ->orWhereNotNull('legacy_region.region_id');
            })
            ->selectRaw("COALESCE(relation_region.region_id, legacy_region.region_id) AS region_id,
                COUNT(*) AS population,
                SUM(CASE WHEN citizens.gender = 'Laki-laki' THEN 1 ELSE 0 END) AS male_count,
                SUM(CASE WHEN citizens.gender = 'Perempuan' THEN 1 ELSE 0 END) AS female_count,
                COUNT(DISTINCT NULLIF(citizens.family_card_number, '')) AS kk_count")
            ->groupByRaw('COALESCE(relation_region.region_id, legacy_region.region_id)')
            ->get()
            ->keyBy('region_id');

        $regions = Region::with('headStaff')
            ->withCount(['rws', 'rts'])
            ->orderByRaw('LOWER(name) ASC')
            ->get()
            ->map(function (Region $region) use ($stats) {
                $s = $stats->get($region->region_id);
                $region->rw_count     = $region->rws_count;
                $region->rt_count     = $region->rts_count;
                $region->population   = (int) ($s?->population ?? 0);
                $region->male_count   = (int) ($s?->male_count ?? 0);
                $region->female_count = (int) ($s?->female_count ?? 0);
                $region->kk_count     = (int) ($s?->kk_count ?? 0);

                return $region;
            });

        return response()->json(['success' => true, 'data' => $regions]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('regions', 'name')],
            'head_name' => 'nullable|string|max:150',
            'head_staff_id' => 'nullable|integer|exists:staff,staff_id',
        ]);

        return response()->json([
            'success' => true,
            'data' => Region::create($data),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);
        $region->update($request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150', Rule::unique('regions', 'name')->ignore($id, 'region_id')],
            'head_name' => 'nullable|string|max:150',
            'head_staff_id' => 'nullable|integer|exists:staff,staff_id',
        ]));

        return response()->json([
            'success' => true,
            'data' => $region,
        ]);
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        StaffAssignment::query()->active()->where('padukuhan_id', $region->region_id)
            ->update(['end_date' => now()->subDay()->toDateString()]);
        $region->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
