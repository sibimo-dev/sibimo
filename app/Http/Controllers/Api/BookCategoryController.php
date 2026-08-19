<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class BookCategoryController extends Controller
{

    public function index(): JsonResponse
    {
        $categorys = BookCategory::query()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori buku berhasil diambil.',
            'data' => $categorys,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_name' => ['required','string','max:100'],
            'description' => ['nullable','string']
        ]);

        $category = BookCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori buku berhasil dibuat.',
            'data' => $category,
        ], 201);
    }

    public function show(int $category_id): JsonResponse
    {
        $category = BookCategory::query()->findOrFail($category_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori buku berhasil diambil.',
            'data' => $category,
        ]);
    }

    public function update(Request $request, int $category_id): JsonResponse
    {
        $category = BookCategory::findOrFail($category_id);

        $validated = $request->validate([
            'category_name' => ['sometimes','required','string','max:100'],
            'description' => ['nullable','string']
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori buku berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    public function destroy(int $category_id): JsonResponse
    {
        $category = BookCategory::findOrFail($category_id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori buku berhasil dihapus.',
        ]);
    }

}
