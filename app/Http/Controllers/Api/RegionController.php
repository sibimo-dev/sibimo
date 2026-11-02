<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\Citizen;
use App\Models\StaffAssignment;

class RegionController extends Controller
{
    public function index()
    {
        // Hitung statistik kependudukan per padukuhan (region) melalui relasi
        // citizens.rt_id -> rts.rw_id -> rws.region_id.
        $stats = Citizen::query()
            ->join('rts', 'citizens.rt_id', '=', 'rts.id')
            ->join('rws', 'rts.rw_id', '=', 'rws.id')
            ->whereRaw("UPPER(citizens.record_type) = 'PENDUDUK'")
            ->where('citizens.status', 'Active')
            ->selectRaw("rws.region_id,
                COUNT(*) AS population,
                SUM(CASE WHEN citizens.gender = 'Laki-laki' THEN 1 ELSE 0 END) AS male_count,
                SUM(CASE WHEN citizens.gender = 'Perempuan' THEN 1 ELSE 0 END) AS female_count,
                COUNT(DISTINCT NULLIF(citizens.family_card_number, '')) AS kk_count")
            ->groupBy('rws.region_id')
            ->get()
            ->keyBy('region_id');

        $regions = Region::with('headStaff')
            ->withCount(['rws', 'rts'])
            ->latest()
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
            'name' => 'required|string|max:150',
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
            'name' => 'sometimes|required|string|max:150',
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
