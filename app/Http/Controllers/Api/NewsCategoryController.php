<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class NewsCategoryController extends Controller
{

    public function index(): JsonResponse
    {
        $categorys = NewsCategory::query()->latest()->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data kategori berita berhasil diambil.',
            'data' => $categorys,
        ]);
    }

 
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_name' => ['required','string','max:100'],
            'slug' => ['required','string','max:100','unique:news_categories,slug']
        ]);

        $category = NewsCategory::create($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Kategori berita berhasil dibuat.',
            'data' => $category,
        ], 201);
    }


    public function show(int $category_id): JsonResponse
    {
        $category = NewsCategory::query()->findOrFail($category_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail kategori berita berhasil diambil.',
            'data' => $category,
        ]);
    }


    public function update(Request $request, int $category_id): JsonResponse
    {
        $category = NewsCategory::findOrFail($category_id);

        $validated = $request->validate([
            'category_name' => ['sometimes','required','string','max:100'],
            'slug' => ['sometimes','required','string','max:100', Rule::unique('news_categories','slug')->ignore($category_id, 'category_id')]
        ]);

        $category->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Kategori berita berhasil diperbarui.',
            'data' => $category,
        ]);
    }


    public function destroy(int $category_id): JsonResponse
    {
        $category = NewsCategory::findOrFail($category_id);
        $category->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Kategori berita berhasil dihapus.',
        ]);
    }

}
