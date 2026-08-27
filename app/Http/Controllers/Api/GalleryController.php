<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class GalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $gallerys = Gallery::query()->with(['uploader'])->latest('uploaded_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data galeri berhasil diambil.',
            'data' => $gallerys,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'image' => ['required','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
        ]);
        $path = $validated['image']->store('galleries', 'public');
        $validated['image'] = Storage::disk('public')->url($path);
        $validated['uploaded_by'] = $request->user()->user_id;
        $validated['uploaded_at'] = now();
    
        $gallery = Gallery::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Galeri berhasil dibuat.',
            'data' => $gallery,
        ], 201);
    }


    public function show(int $gallery_id): JsonResponse
    {
        $gallery = Gallery::query()->with(['uploader'])->findOrFail($gallery_id);

        return response()->json([
            'success' => true,
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
            'image' => ['sometimes','required','file','mimes:jpg,jpeg,png,webp,gif','max:5120']
        ]);

        if (isset($validated['image'])) {
            if ($gallery->image && !str_starts_with($gallery->image, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', parse_url($gallery->image, PHP_URL_PATH)));
            }
            $validated['image'] = Storage::disk('public')->url($validated['image']->store('galleries', 'public'));
        }

        $gallery->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Galeri berhasil diperbarui.',
            'data' => $gallery,
        ]);
    }


    public function destroy(int $gallery_id): JsonResponse
    {
        $gallery = Gallery::findOrFail($gallery_id);
        if ($gallery->image && !str_starts_with($gallery->image, 'http')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', parse_url($gallery->image, PHP_URL_PATH)));
        }
        $gallery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Galeri berhasil dihapus.',
        ]);
    }

}
