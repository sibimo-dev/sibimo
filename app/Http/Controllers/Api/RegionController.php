<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Region::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'head_name' => 'nullable|string|max:150',
            'rw_count' => 'integer|min:0',
            'rt_count' => 'integer|min:0',
            'kk_count' => 'integer|min:0',
            'population' => 'integer|min:0',
            'male_count' => 'integer|min:0',
            'female_count' => 'integer|min:0',
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
            'rw_count' => 'integer|min:0',
            'rt_count' => 'integer|min:0',
            'kk_count' => 'integer|min:0',
            'population' => 'integer|min:0',
            'male_count' => 'integer|min:0',
            'female_count' => 'integer|min:0',
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
