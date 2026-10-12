<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class NewsCategoryController extends Controller
{

    public function index(): JsonResponse
    {
        $categorys = NewsCategory::query()->orderBy('category_name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berita berhasil diambil.',
            'data' => $categorys,
        ]);
    }

 
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_name' => ['required','string','max:100', Rule::unique('news_categories', 'category_name')],
            'slug' => ['nullable','string','max:100', Rule::unique('news_categories', 'slug')],
        ]);
        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['category_name']);

        $category = NewsCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berita berhasil dibuat.',
            'data' => $category,
        ], 201);
    }


    public function show(int $category_id): JsonResponse
    {
        $category = NewsCategory::query()->findOrFail($category_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori berita berhasil diambil.',
            'data' => $category,
        ]);
    }


    public function update(Request $request, int $category_id): JsonResponse
    {
        $category = NewsCategory::findOrFail($category_id);

        $validated = $request->validate([
            'category_name' => ['sometimes','required','string','max:100', Rule::unique('news_categories', 'category_name')->ignore($category_id, 'category_id')],
            'slug' => ['sometimes','nullable','string','max:100', Rule::unique('news_categories','slug')->ignore($category_id, 'category_id')],
        ]);
        if (array_key_exists('category_name', $validated) && !array_key_exists('slug', $validated)) {
            $validated['slug'] = Str::slug($validated['category_name']);
        } elseif (array_key_exists('slug', $validated)) {
            $validated['slug'] = Str::slug($validated['slug'] ?: $category->category_name);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berita berhasil diperbarui.',
            'data' => $category,
        ]);
    }


    public function destroy(int $category_id): JsonResponse
    {
        $category = NewsCategory::findOrFail($category_id);

        if ($category->news()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih dipakai oleh berita.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berita berhasil dihapus.',
        ]);
    }

}
