<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LegalProductCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = LegalProductCategory::query()->withCount('legalProducts')->orderBy('category_name')->get();
        return response()->json(['success' => true, 'message' => 'Data kategori berhasil diambil.', 'data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:legal_product_categories,category_name'],
            'description'   => ['nullable', 'string'],
        ]);

        $slug = Str::slug($data['category_name']);
        $base = $slug; $i = 2;
        while (LegalProductCategory::where('slug', $slug)->exists()) $slug = $base . '-' . $i++;

        $item = LegalProductCategory::create([...$data, 'slug' => $slug]);
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dibuat.', 'data' => $item], 201);
    }

    public function update(Request $request, int $category_id): JsonResponse
    {
        $item = LegalProductCategory::query()->findOrFail($category_id);
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:legal_product_categories,category_name,' . $category_id . ',category_id'],
            'description'   => ['nullable', 'string'],
        ]);
        // slug sengaja tidak diubah, karena dipakai sebagai nilai kolom `category` di produk hukum
        $item->update($data);
        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui.', 'data' => $item]);
    }

    public function destroy(int $category_id): JsonResponse
    {
        $item = LegalProductCategory::query()->findOrFail($category_id);
        if ($item->legalProducts()->exists()) {
            return response()->json(['success' => false, 'message' => 'Kategori masih dipakai produk hukum.'], 422);
        }
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }
}