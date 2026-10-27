<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DevelopmentCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DevelopmentCategory::query()->withCount('developments')->orderBy('category_name')->get();
        return response()->json(['success' => true, 'message' => 'Data kategori berhasil diambil.', 'data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:development_categories,category_name'],
        ]);

        $base = Str::limit(Str::slug($data['category_name']), 40, '') ?: 'kategori';
        $slug = $base; $i = 2;
        while (DevelopmentCategory::where('slug', $slug)->exists()) $slug = $base . '-' . $i++;

        $item = DevelopmentCategory::create([...$data, 'slug' => $slug]);
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dibuat.', 'data' => $item], 201);
    }

    public function update(Request $request, int $category_id): JsonResponse
    {
        $item = DevelopmentCategory::query()->findOrFail($category_id);
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:development_categories,category_name,' . $category_id . ',category_id'],
        ]);
        // slug tidak diubah, karena dipakai sebagai nilai kolom `category` di developments
        $item->update($data);
        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui.', 'data' => $item]);
    }

    public function destroy(int $category_id): JsonResponse
    {
        $item = DevelopmentCategory::query()->findOrFail($category_id);
        if ($item->developments()->exists()) {
            return response()->json(['success' => false, 'message' => 'Kategori masih dipakai data pembangunan.'], 422);
        }
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }
}