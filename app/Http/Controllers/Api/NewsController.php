<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

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
            'category_id'    => ['nullable', 'exists:news_categories,category_id'],
            'category_name'  => ['nullable', 'string', 'max:100'],
            'author_id'      => ['nullable', 'exists:users,user_id'],
            'title'          => ['required', 'string', 'max:200'],
            'slug'           => ['required', 'string', 'max:200', 'unique:news,slug'],
            'content'        => ['required', 'string'],
            'thumbnail'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'status'         => ['nullable', Rule::in(['Draft', 'Published', 'Archived'])],
            'published_at'   => ['nullable', 'date'],
            'excerpt'        => ['nullable', 'string', 'max:255'],
            'content_blocks' => ['nullable', 'array'],
            'is_popular'     => ['boolean'],
            'is_pinned'      => ['boolean'],
        ]);

        $validated['author_id'] = $request->user()->user_id;

        if (empty($validated['category_id']) && !empty($validated['category_name'])) {
            $category = NewsCategory::firstOrCreate(
                ['slug' => str($validated['category_name'])->slug()],
                ['category_name' => $validated['category_name']]
            );
            $validated['category_id'] = $category->category_id;
        }
        unset($validated['category_name']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->storeThumbnail($request->file('thumbnail'));
        }

        if (!empty($validated['content'])) {
            $validated['content'] = $this->processContentImages($validated['content']);
        }

        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dibuat.',
            'data'    => $news,
        ], 201);
    }


    public function show(int $news_id): JsonResponse
    {
        $news = News::query()->with(['category', 'author'])->findOrFail($news_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail berita berhasil diambil.',
            'data'    => $news,
        ]);
    }


    public function update(Request $request, int $news_id): JsonResponse
    {
        $news = News::findOrFail($news_id);

        $validated = $request->validate([
            'category_id'    => ['nullable', 'exists:news_categories,category_id'],
            'category_name'  => ['nullable', 'string', 'max:100'],
            'title'          => ['sometimes', 'required', 'string', 'max:200'],
            'slug'           => ['sometimes', 'required', 'string', 'max:200', Rule::unique('news', 'slug')->ignore($news_id, 'news_id')],
            'content'        => ['sometimes', 'required', 'string'],
            'thumbnail'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'status'         => ['nullable', Rule::in(['Draft', 'Published', 'Archived'])],
            'published_at'   => ['nullable', 'date'],
            'excerpt'        => ['nullable', 'string', 'max:255'],
            'content_blocks' => ['nullable', 'array'],
            'is_popular'     => ['boolean'],
            'is_pinned'      => ['boolean'],
        ]);

        if (empty($validated['category_id']) && !empty($validated['category_name'])) {
            $category = NewsCategory::firstOrCreate(
                ['slug' => str($validated['category_name'])->slug()],
                ['category_name' => $validated['category_name']]
            );
            $validated['category_id'] = $category->category_id;
        }
        unset($validated['category_name']);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama sebelum simpan yang baru
            $this->deleteThumbnail($news->thumbnail);
            $validated['thumbnail'] = $this->storeThumbnail($request->file('thumbnail'));
        }

        if (isset($validated['content'])) {
            $this->deleteRemovedContentImages($news->content, $validated['content']);
            $validated['content'] = $this->processContentImages($validated['content']);
        }

        $news->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui.',
            'data'    => $news,
        ]);
    }


    public function destroy(int $news_id): JsonResponse
    {
        $news = News::findOrFail($news_id);

        // Hapus file thumbnail dan gambar konten dari storage sebelum hapus record
        $this->deleteThumbnail($news->thumbnail);
        $this->deleteContentImages($news->content);

        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus.',
        ]);
    }


    // -------------------------------------------------------------------------
    // Helper: compress & simpan thumbnail, return URL publik
    // -------------------------------------------------------------------------
    private function storeThumbnail(\Illuminate\Http\UploadedFile $file): string
    {
        $filename = 'news/' . uniqid('thumb_', true) . '.webp';

        try {
            // Kompres: resize max 1200px lebar, kualitas 80%, convert ke webp
            $compressed = Image::decode($file)
                ->scaleDown(width: 1200)
                ->encode(new WebpEncoder(quality: 80));

            Storage::disk('public')->put($filename, (string) $compressed);

            return Storage::disk('public')->url($filename);
        } catch (\Throwable $e) {
            \Log::warning('Gagal kompres thumbnail: ' . $e->getMessage());
            return Storage::disk('public')->url($file->store('news', 'public'));
        }
    }

    // -------------------------------------------------------------------------
    // Helper: kompres dan simpan gambar base64 yang disematkan di isi konten
    // -------------------------------------------------------------------------
    private function processContentImages(string $content): string
    {
        return preg_replace_callback(
            '/<img[^>]+src=["\'](data:image\/[^;]+;base64,[^"\']+)["\'][^>]*>/i',
            function ($matches) {
                $fullDataUri = $matches[1];
                try {
                    $img = Image::decodeDataUri($fullDataUri);
                    $img->scaleDown(width: 1200);
                    $encoded = $img->encode(new WebpEncoder(quality: 80));

                    $filename = 'news/' . uniqid('content_', true) . '.webp';
                    Storage::disk('public')->put($filename, (string) $encoded);

                    $url = Storage::disk('public')->url($filename);
                    return str_replace($fullDataUri, $url, $matches[0]);
                } catch (\Throwable $e) {
                    \Log::warning('Gagal proses gambar konten: ' . $e->getMessage());
                    return $matches[0];
                }
            },
            $content
        );
    }

    // -------------------------------------------------------------------------
    // Helper: hapus file thumbnail dari storage (terima URL atau path relatif)
    // -------------------------------------------------------------------------
    private function deleteThumbnail(?string $thumbnail): void
    {
        if (!$thumbnail) return;

        // Ambil path relatif dari URL lengkap (misal: /storage/news/thumb_xxx.webp → news/thumb_xxx.webp)
        $path = preg_replace('#^.*/storage/#', '', $thumbnail);

        // Jangan hapus aset bawaan seeder
        if (preg_match('#^news/news-\d+\.#i', $path)) return;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // -------------------------------------------------------------------------
    // Helper: hapus semua file gambar yang tersimpan di konten berita
    // -------------------------------------------------------------------------
    private function deleteContentImages(?string $content): void
    {
        if (empty($content)) return;

        preg_match_all('#/storage/(news/[^"\'\s>]+)#i', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $path) {
                if (preg_match('#^news/news-\d+\.#i', $path)) continue;
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Helper: hapus gambar konten lama yang sudah dihapus/dihilangkan saat edit
    // -------------------------------------------------------------------------
    private function deleteRemovedContentImages(?string $oldContent, ?string $newContent): void
    {
        if (empty($oldContent)) return;

        preg_match_all('#/storage/(news/[^"\'\s>]+)#i', $oldContent, $oldMatches);
        $oldPaths = $oldMatches[1] ?? [];
        if (empty($oldPaths)) return;

        preg_match_all('#/storage/(news/[^"\'\s>]+)#i', (string) $newContent, $newMatches);
        $newPaths = array_flip($newMatches[1] ?? []);

        foreach ($oldPaths as $path) {
            if (!isset($newPaths[$path])) {
                if (preg_match('#^news/news-\d+\.#i', $path)) continue;
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }
}
