<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GalleryCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil.',
            'data' => GalleryCategory::orderBy('category_id')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:gallery_categories,name'],
        ]);

        $base = Str::slug($validated['name']) ?: 'kategori';
        $slug = $base;
        $i = 2;
        while (GalleryCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $category = GalleryCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat.',
            'data' => $category,
        ], 201);
    }

    // Hanya name yang berubah; slug tetap agar galeri lama tidak kehilangan kategorinya
    public function update(Request $request, int $category_id): JsonResponse
    {
        $category = GalleryCategory::findOrFail($category_id);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('gallery_categories', 'name')->ignore($category->category_id, 'category_id'),
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
        $category = GalleryCategory::findOrFail($category_id);

        if (Gallery::where('category', $category->slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori masih dipakai oleh foto di galeri.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}