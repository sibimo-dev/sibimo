<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class NewsController extends Controller
{

    public function index(): JsonResponse
    {
        $newss = News::query()->with(['category', 'author'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berita berhasil diambil.',
            'data' => $newss,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required','exists:news_categories,category_id'],
            'author_id' => ['required','exists:users,user_id'],
            'title' => ['required','string','max:200'],
            'slug' => ['required','string','max:200','unique:news,slug'],
            'content' => ['required','string'],
            'thumbnail' => ['nullable','string','max:225'],
            'status' => ['nullable', Rule::in(['Draft','Published','Archived'])],
            'published_at' => ['nullable','date']
        ]);

        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dibuat.',
            'data' => $news,
        ], 201);
    }


    public function show(int $news_id): JsonResponse
    {
        $news = News::query()->with(['category', 'author'])->findOrFail($news_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail berita berhasil diambil.',
            'data' => $news,
        ]);
    }


    public function update(Request $request, int $news_id): JsonResponse
    {
        $news = News::findOrFail($news_id);

        $validated = $request->validate([
            'category_id' => ['sometimes','required','exists:news_categories,category_id'],
            'title' => ['sometimes','required','string','max:200'],
            'slug' => ['sometimes','required','string','max:200', Rule::unique('news','slug')->ignore($news_id, 'news_id')],
            'content' => ['sometimes','required','string'],
            'thumbnail' => ['nullable','string','max:225'],
            'status' => ['nullable', Rule::in(['Draft','Published','Archived'])],
            'published_at' => ['nullable','date']
        ]);

        $news->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui.',
            'data' => $news,
        ]);
    }


    public function destroy(int $news_id): JsonResponse
    {
        $news = News::findOrFail($news_id);
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus.',
        ]);
    }

}
