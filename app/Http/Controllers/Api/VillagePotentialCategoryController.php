<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillagePotential;
use App\Models\VillagePotentialCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VillagePotentialCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil.',
            'data' => VillagePotentialCategory::orderBy('category_id')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:village_potential_categories,name'],
        ]);

        $base = Str::slug($validated['name']) ?: 'kategori';
        $slug = $base;
        $i = 2;
        while (VillagePotentialCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $category = VillagePotentialCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat.',
            'data' => $category,
        ], 201);
    }

    // Hanya name yang berubah; slug tetap agar data lama tidak kehilangan kategorinya
    public function update(Request $request, int $category_id): JsonResponse
    {
        $category = VillagePotentialCategory::findOrFail($category_id);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('village_potential_categories', 'name')->ignore($category->category_id, 'category_id'),
            ],
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    public function destroy(int $category_id): JsonResponse
    {
        $category = VillagePotentialCategory::findOrFail($category_id);

        if (VillagePotential::where('category', $category->slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori masih dipakai oleh data potensi.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}