<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceHour;
use Illuminate\Http\Request;

class ServiceHourController extends Controller
{
    /**
     * GET /api/service-hours
     * Publik — dipakai teks berjalan di situs public.
     */
    public function index()
    {
        $hours = ServiceHour::orderBy('day_of_week')->get();
        return response()->json(['data' => $hours]);
    }

    /**
     * PUT /api/service-hours
     * Admin — simpan semua jam pelayanan sekaligus (upsert per hari).
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'hours'               => 'required|array|min:1',
            'hours.*.day_of_week' => 'required|integer|min:1|max:7',
            'hours.*.is_closed'   => 'required|boolean',
            'hours.*.open_time'   => 'nullable|string',
            'hours.*.close_time'  => 'nullable|string',
        ]);

        foreach ($validated['hours'] as $row) {
            $isClosed = (bool) $row['is_closed'];
            $openTime = $isClosed ? null : (!empty($row['open_time']) ? substr(trim($row['open_time']), 0, 5) : null);
            $closeTime = $isClosed ? null : (!empty($row['close_time']) ? substr(trim($row['close_time']), 0, 5) : null);

            ServiceHour::updateOrCreate(
                ['day_of_week' => $row['day_of_week']],
                [
                    'is_closed'  => $isClosed,
                    'open_time'  => $openTime,
                    'close_time' => $closeTime,
                ]
            );
        }

        return response()->json([
            'message' => 'Jam pelayanan berhasil disimpan.',
            'data'    => ServiceHour::orderBy('day_of_week')->get(),
        ]);
    }
}
