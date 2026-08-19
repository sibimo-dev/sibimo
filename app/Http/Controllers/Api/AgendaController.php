<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class AgendaController extends Controller
{
    public function index(): JsonResponse
    {
        $agendas = Agenda::query()->with(['creator'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data agenda berhasil diambil.',
            'data' => $agendas,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'event_date' => ['required','date'],
            'start_time' => ['nullable','date_format:H:i'],
            'end_time' => ['nullable','date_format:H:i'],
            'location' => ['nullable','string','max:255'],
        ]);

        $validated['created_by'] = $request->user()->user_id;

        $agenda = Agenda::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dibuat.',
            'data' => $agenda,
        ], 201);
    }

    public function show(int $agenda_id): JsonResponse
    {
        $agenda = Agenda::query()->with(['creator'])->findOrFail($agenda_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail agenda berhasil diambil.',
            'data' => $agenda,
        ]);
    }

    public function update(Request $request, int $agenda_id): JsonResponse
    {
        $agenda = Agenda::findOrFail($agenda_id);

        $validated = $request->validate([
            'title' => ['sometimes','required','string','max:200'],
            'description' => ['nullable','string'],
            'event_date' => ['sometimes','required','date'],
            'start_time' => ['nullable','date_format:H:i'],
            'end_time' => ['nullable','date_format:H:i'],
            'location' => ['nullable','string','max:255']
        ]);

        $agenda->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil diperbarui.',
            'data' => $agenda,
        ]);
    }

    public function destroy(int $agenda_id): JsonResponse
    {
        $agenda = Agenda::findOrFail($agenda_id);
        $agenda->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dihapus.',
        ]);
    }

}
