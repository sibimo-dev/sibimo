<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\Citizen;

class RegionController extends Controller
{
    public function index()
    {
        // Hanya penduduk yang masih hidup dan tinggal di sini (bukan orang luar / pindah / mati).
        $stats = Citizen::query()
            ->whereRaw("UPPER(record_type) = 'PENDUDUK'")
            ->where('status', 'Active')
            ->whereNotNull('dusun')
            ->selectRaw("dusun,
                COUNT(*) AS population,
                SUM(CASE WHEN gender = 'Laki-laki' THEN 1 ELSE 0 END) AS male_count,
                SUM(CASE WHEN gender = 'Perempuan' THEN 1 ELSE 0 END) AS female_count,
                COUNT(DISTINCT NULLIF(family_card_number, '')) AS kk_count")
            ->groupBy('dusun')
            ->get()
            ->keyBy(fn ($row) => mb_strtolower(trim($row->dusun)));

        $regions = Region::with('headStaff')
            ->withCount(['rws', 'rts'])
            ->latest()
            ->get()
            ->map(function (Region $region) use ($stats) {
                $s = $stats->get(mb_strtolower(trim($region->name)));
                $region->rw_count     = $region->rws_count;
                $region->rt_count     = $region->rts_count;
                $region->population   = (int) ($s->population ?? 0);
                $region->male_count   = (int) ($s->male_count ?? 0);
                $region->female_count = (int) ($s->female_count ?? 0);
                $region->kk_count     = (int) ($s->kk_count ?? 0);

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
        Region::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
