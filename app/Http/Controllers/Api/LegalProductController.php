<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LegalProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data produk hukum berhasil diambil.',
            'data' => LegalProduct::query()->latest('year')->latest('legal_product_id')->get(),
        ]);
    }

    public function show(int $legal_product_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail produk hukum berhasil diambil.',
            'data' => LegalProduct::query()->findOrFail($legal_product_id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $product = $this->save($request, new LegalProduct());

        return response()->json([
            'success' => true,
            'message' => 'Produk hukum berhasil dibuat.',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, int $legal_product_id): JsonResponse
    {
        $product = $this->save($request, LegalProduct::query()->findOrFail($legal_product_id));

        return response()->json([
            'success' => true,
            'message' => 'Produk hukum berhasil diperbarui.',
            'data' => $product,
        ]);
    }

    public function destroy(int $legal_product_id): JsonResponse
    {
        $product = LegalProduct::query()->findOrFail($legal_product_id);
        $this->deleteDocument($product->document);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk hukum berhasil dihapus.',
        ]);
    }

    public function download(int $legal_product_id): Response
    {
        $product = LegalProduct::query()->findOrFail($legal_product_id);
        abort_unless($product->document, 404, 'Dokumen produk hukum belum tersedia.');

        $path = $this->storagePath($product->document);
        abort_unless($path && Storage::disk('public')->exists($path), 404, 'Dokumen produk hukum tidak ditemukan.');

        return Storage::disk('public')->download($path, basename($path));
    }

    private function save(Request $request, LegalProduct $product): LegalProduct
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['perkal', 'sk-lurah'])],
            'status' => ['required', Rule::in(['berlaku', 'dicabut'])],
            'number' => ['nullable', 'string', 'max:150'],
            'year' => ['required', 'integer', 'min:1945', 'max:2100'],
            'description' => ['nullable', 'string'],
            'document' => [$product->exists ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $oldDocument = $product->document;
        $attributes = collect($validated)->except(['document'])->all();
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('legal-products', 'public');
            $attributes['document'] = $this->mediaUrl($path);
        }

        $product->fill($attributes)->save();
        if ($request->hasFile('document')) $this->deleteDocument($oldDocument);

        return $product->fresh();
    }

    private function mediaUrl(string $path): string
    {
        $url = Storage::disk('public')->url($path);
        return str_starts_with($url, '/')
            ? rtrim((string) config('app.url'), '/') . $url
            : $url;
    }

    private function storagePath(?string $url): ?string
    {
        if (!$url || !str_contains($url, '/storage/legal-products/')) return null;
        return str_replace('/storage/', '', (string) parse_url($url, PHP_URL_PATH));
    }

    private function deleteDocument(?string $url): void
    {
        $path = $this->storagePath($url);
        if ($path) Storage::disk('public')->delete($path);
    }
}
