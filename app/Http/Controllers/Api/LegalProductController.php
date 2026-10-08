<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'data' => $this->present($product->fresh()),
        ]);
    }

    public function destroy(int $legal_product_id): JsonResponse
    {
        $product = LegalProduct::findOrFail($legal_product_id);

        $this->deleteFile($product->document);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk hukum berhasil dihapus.',
        ]);
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