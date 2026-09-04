<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


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
            'category_id' => ['nullable','exists:news_categories,category_id'],
            'category_name' => ['nullable','string','max:100'],
            'author_id' => ['nullable','exists:users,user_id'],
            'title' => ['required','string','max:200'],
            'slug' => ['required','string','max:200','unique:news,slug'],
            'content' => ['required','string'],
            'thumbnail' => ['nullable','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'status' => ['nullable', Rule::in(['Draft','Published','Archived'])],
            'published_at' => ['nullable','date'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content_blocks' => ['nullable', 'array'], // format: [{ "type": "text", "value": "..." }, { "type": "image", "src": "...", "caption": "..." }]
            'is_popular' => ['boolean'],
            'is_pinned' => ['boolean']
        ]);

        $validated['author_id'] = $request->user()->user_id;
        if (empty($validated['category_id']) && !empty($validated['category_name'])) {
            $category = NewsCategory::firstOrCreate(['slug' => str($validated['category_name'])->slug()], ['category_name' => $validated['category_name']]);
            $validated['category_id'] = $category->category_id;
        }
        unset($validated['category_name']);
        if (isset($validated['thumbnail'])) $validated['thumbnail'] = Storage::disk('public')->url($validated['thumbnail']->store('news', 'public'));
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
            'category_id' => ['nullable','exists:news_categories,category_id'],
            'category_name' => ['nullable','string','max:100'],
            'title' => ['sometimes','required','string','max:200'],
            'slug' => ['sometimes','required','string','max:200', Rule::unique('news','slug')->ignore($news_id, 'news_id')],
            'content' => ['sometimes','required','string'],
            'thumbnail' => ['nullable','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'status' => ['nullable', Rule::in(['Draft','Published','Archived'])],
            'published_at' => ['nullable','date'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content_blocks' => ['nullable', 'array'], 
            'is_popular' => ['boolean'],
            'is_pinned' => ['boolean']
        ]);

        if (empty($validated['category_id']) && !empty($validated['category_name'])) {
            $category = NewsCategory::firstOrCreate(['slug' => str($validated['category_name'])->slug()], ['category_name' => $validated['category_name']]);
            $validated['category_id'] = $category->category_id;
        }
        unset($validated['category_name']);
        if (isset($validated['thumbnail'])) $validated['thumbnail'] = Storage::disk('public')->url($validated['thumbnail']->store('news', 'public'));
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
