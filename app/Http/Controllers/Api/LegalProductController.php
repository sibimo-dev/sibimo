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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class LegalProductController extends Controller
{
    private const CATEGORIES = ['perkal', 'sk-lurah'];
    private const STATUSES   = ['berlaku', 'dicabut'];

    public function index(): JsonResponse
    {
        $items = LegalProduct::query()
            ->orderByDesc('year')
            ->orderBy('title')
            ->get()
            ->map(fn (LegalProduct $p) => $this->present($p));

        return response()->json([
            'success' => true,
            'message' => 'Data produk hukum berhasil diambil.',
            'data' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        $data = collect($validated)->except('document')->all();
        $data['created_by'] = $request->user()->user_id;
        $data['status'] = $data['status'] ?? 'berlaku';

        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document')->store('legal-products', 'public');
        }

        $product = LegalProduct::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk hukum berhasil dibuat.',
            'data' => $this->present($product),
        ], 201);
    }

    public function show(int $legal_product_id): JsonResponse
    {
        $product = LegalProduct::findOrFail($legal_product_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail produk hukum berhasil diambil.',
            'data' => $this->present($product),
        ]);
    }

    public function download(int $legal_product_id)
    {
        $product = LegalProduct::findOrFail($legal_product_id);
        $disk = Storage::disk('public');

        abort_unless($product->document && $disk->exists($product->document), 404, 'Dokumen tidak ditemukan.');

        $ext  = pathinfo($product->document, PATHINFO_EXTENSION) ?: 'pdf';
        $name = (Str::slug($product->title) ?: 'produk-hukum') . '.' . $ext;

        return $disk->download($product->document, $name);
    }

    public function update(Request $request, int $legal_product_id): JsonResponse
    {
        $product = $this->save($request, LegalProduct::query()->findOrFail($legal_product_id));
        $product = LegalProduct::findOrFail($legal_product_id);

        $validated = $request->validate($this->rules(partial: true));
        $data = collect($validated)->except('document')->all();

        if ($request->hasFile('document')) {
            $this->deleteFile($product->document);
            $data['document'] = $request->file('document')->store('legal-products', 'public');
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk hukum berhasil diperbarui.',
            'data' => $product,
            'data' => $this->present($product->fresh()),
        ]);
    }

    public function destroy(int $legal_product_id): JsonResponse
    {
        $product = LegalProduct::query()->findOrFail($legal_product_id);
        $this->deleteDocument($product->document);
        $product = LegalProduct::findOrFail($legal_product_id);

        $this->deleteFile($product->document);
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
    private function rules(bool $partial = false): array
    {
        $required = fn (array $rules) => $partial ? array_merge(['sometimes'], $rules) : $rules;

        return [
            'title'       => $required(['required', 'string', 'max:255']),
            'category'    => $required(['required', Rule::in(self::CATEGORIES)]),
            'status'      => ['nullable', Rule::in(self::STATUSES)],
            'number'      => ['nullable', 'string', 'max:100'],
            'year'        => $required(['required', 'integer', 'between:1945,2100']),
            'description' => ['nullable', 'string'],
            'document'    => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function present(LegalProduct $p): array
    {
        return [
            'legal_product_id' => $p->legal_product_id,
            'title'            => $p->title,
            'category'         => $p->category,
            'status'           => $p->status,
            'number'           => $p->number,
            'year'             => $p->year,
            'description'      => $p->description,
            'document'         => $p->document ? Storage::disk('public')->url($p->document) : null,
            'created_at'       => $p->created_at,
        ];
    }
}
