<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class GalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $gallerys = Gallery::query()->with(['uploader'])->latest('uploaded_at')->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data galeri berhasil diambil.',
            'data' => $gallerys,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'image' => ['required','string','max:255'],
            'uploaded_by' => ['required','exists:users,user_id']
        ]);

        $gallery = Gallery::create($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Galeri berhasil dibuat.',
            'data' => $gallery,
        ], 201);
    }


    public function show(int $gallery_id): JsonResponse
    {
        $gallery = Gallery::query()->with(['uploader'])->findOrFail($gallery_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail galeri berhasil diambil.',
            'data' => $gallery,
        ]);
    }

 
    public function update(Request $request, int $gallery_id): JsonResponse
    {
        $gallery = Gallery::findOrFail($gallery_id);

        $validated = $request->validate([
            'title' => ['sometimes','required','string','max:200'],
            'description' => ['nullable','string'],
            'image' => ['sometimes','required','string','max:255']
        ]);

        $gallery->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Galeri berhasil diperbarui.',
            'data' => $gallery,
        ]);
    }


    public function destroy(int $gallery_id): JsonResponse
    {
        $gallery = Gallery::findOrFail($gallery_id);
        $gallery->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Galeri berhasil dihapus.',
        ]);
    }

}
