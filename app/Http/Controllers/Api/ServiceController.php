<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ServiceController extends Controller
{

    public function index(): JsonResponse
    {
        $services = Service::query()->latest()->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data layanan berhasil diambil.',
            'data' => $services,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required','string','max:150'],
            'description' => ['nullable','string'],
            'icon' => ['nullable','string','max:255'],
            'sort_order' => ['integer'],
            'is_active' => ['boolean']
        ]);

        $service = Service::create($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Layanan berhasil dibuat.',
            'data' => $service,
        ], 201);
    }


    public function show(int $service_id): JsonResponse
    {
        $service = Service::query()->findOrFail($service_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail layanan berhasil diambil.',
            'data' => $service,
        ]);
    }


    public function update(Request $request, int $service_id): JsonResponse
    {
        $service = Service::findOrFail($service_id);

        $validated = $request->validate([
            'title' => ['sometimes','required','string','max:150'],
            'description' => ['nullable','string'],
            'icon' => ['nullable','string','max:255'],
            'sort_order' => ['integer'],
            'is_active' => ['boolean']
        ]);

        $service->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Layanan berhasil diperbarui.',
            'data' => $service,
        ]);
    }


    public function destroy(int $service_id): JsonResponse
    {
        $service = Service::findOrFail($service_id);
        $service->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Layanan berhasil dihapus.',
        ]);
    }

}
