<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ProfileSectionController extends Controller
{

    public function index(): JsonResponse
    {
        $sections = ProfileSection::query()->with(['contents'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data section profil berhasil diambil.',
            'data' => $sections,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'section_name' => ['required','string','max:100'],
            'slug' => ['required','string','max:100','unique:profile_sections,slug'],
            'sort_order' => ['integer'],
            'is_active' => ['boolean']
        ]);

        $section = ProfileSection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section profil berhasil dibuat.',
            'data' => $section,
        ], 201);
    }


    public function show(int $section_id): JsonResponse
    {
        $section = ProfileSection::query()->with(['contents'])->findOrFail($section_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail section profil berhasil diambil.',
            'data' => $section,
        ]);
    }


    public function update(Request $request, int $section_id): JsonResponse
    {
        $section = ProfileSection::findOrFail($section_id);

        $validated = $request->validate([
            'section_name' => ['sometimes','required','string','max:100'],
            'slug' => ['sometimes','required','string','max:100', Rule::unique('profile_sections','slug')->ignore($section_id, 'section_id')],
            'sort_order' => ['integer'],
            'is_active' => ['boolean']
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section profil berhasil diperbarui.',
            'data' => $section,
        ]);
    }

    public function destroy(int $section_id): JsonResponse
    {
        $section = ProfileSection::findOrFail($section_id);
        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Section profil berhasil dihapus.',
        ]);
    }

}
