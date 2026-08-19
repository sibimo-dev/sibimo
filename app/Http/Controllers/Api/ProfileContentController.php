<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ProfileContentController extends Controller
{

    public function index(): JsonResponse
    {
        $contents = ProfileContent::query()->with(['section', 'publisher'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data konten profil berhasil diambil.',
            'data' => $contents,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'section_id' => ['required','exists:profile_sections,section_id'],
            'title' => ['required','string','max:200'],
            'content' => ['nullable','string'],
            'thumbnail' => ['nullable','string','max:255'],
            'published_by' => ['nullable','exists:users,user_id'],
            'status' => ['nullable', Rule::in(['Draft','Published'])],
            'published_at' => ['nullable','date']
        ]);

        $content = ProfileContent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konten profil berhasil dibuat.',
            'data' => $content,
        ], 201);
    }

    public function show(int $profileContent_id): JsonResponse
    {
        $content = ProfileContent::query()->with(['section', 'publisher'])->findOrFail($profileContent_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail konten profil berhasil diambil.',
            'data' => $content,
        ]);
    }

    public function update(Request $request, int $profileContent_id): JsonResponse
    {
        $content = ProfileContent::findOrFail($profileContent_id);

        $validated = $request->validate([
            'section_id' => ['sometimes','required','exists:profile_sections,section_id'],
            'title' => ['sometimes','required','string','max:200'],
            'content' => ['nullable','string'],
            'thumbnail' => ['nullable','string','max:255'],
            'published_by' => ['nullable','exists:users,user_id'],
            'status' => ['nullable', Rule::in(['Draft','Published'])],
            'published_at' => ['nullable','date']
        ]);

        $content->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konten profil berhasil diperbarui.',
            'data' => $content,
        ]);
    }

    public function destroy(int $profileContent_id): JsonResponse
    {
        $content = ProfileContent::findOrFail($profileContent_id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Konten profil berhasil dihapus.',
        ]);
    }

}
