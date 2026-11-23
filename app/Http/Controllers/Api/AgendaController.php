<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class AgendaController extends Controller
{
    public function index(): JsonResponse
    {
        $agendas = Agenda::query()->with(['creator'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data agenda berhasil diambil.',
            'data' => $agendas->map(fn (Agenda $agenda) => $this->serialize($agenda)),
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
            'attendee' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx', 'max:5120'],
            'status' => ['nullable', Rule::in(['Draft', 'Published', 'Archived'])],
        ]);

        $validated['status'] = $validated['status'] ?? 'Draft';
        if (isset($validated['attachment'])) {
            $validated['attachment_path'] = $validated['attachment']->store('agendas', 'public');
            unset($validated['attachment']);
        }

        $validated['created_by'] = $request->user()->user_id;

        $agenda = Agenda::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dibuat.',
            'data' => $this->serialize($agenda),
        ], 201);
    }

    public function show(int $agenda_id): JsonResponse
    {
        $agenda = Agenda::query()->with(['creator'])->findOrFail($agenda_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail agenda berhasil diambil.',
            'data' => $this->serialize($agenda),
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
            'location' => ['nullable','string','max:255'],
            'attendee' => ['nullable', 'string', 'max:255'],
            'attachment' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx', 'max:5120'],
            'remove_attachment' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['Draft', 'Published', 'Archived'])],
        ]);

        if ($request->boolean('remove_attachment')) {
            $this->deleteAttachment($agenda->attachment_path);
            $validated['attachment_path'] = null;
        }

        if (isset($validated['attachment'])) {
            $this->deleteAttachment($agenda->attachment_path);
            $validated['attachment_path'] = $validated['attachment']->store('agendas', 'public');
            unset($validated['attachment']);
        }
        unset($validated['remove_attachment']);

        $agenda->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil diperbarui.',
            'data' => $this->serialize($agenda),
        ]);
    }

    public function destroy(int $agenda_id): JsonResponse
    {
        $agenda = Agenda::findOrFail($agenda_id);
        $this->deleteAttachment($agenda->attachment_path);
        $agenda->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dihapus.',
        ]);
    }

    private function serialize(Agenda $agenda): array
    {
        $data = $agenda->toArray();
        $path = $agenda->attachment_path;
        $url = $path
            ? (preg_match('/^https?:\/\//i', $path) ? $path : Storage::disk('public')->url($path))
            : null;

        $data['attachment_url'] = $url;
        $data['attachment_name'] = $path ? basename(parse_url($path, PHP_URL_PATH) ?: $path) : null;
        $data['image'] = $url && preg_match('/\.(png|jpe?g|gif|webp|avif)(\?.*)?$/i', $url) ? $url : null;

        return $data;
    }

    private function deleteAttachment(?string $path): void
    {
        if (!$path || preg_match('/^https?:\/\//i', $path)) {
            return;
        }

        Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $path), '/'));
    }

}
