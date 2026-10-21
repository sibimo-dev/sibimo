<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LegalProductController extends Controller
{
    private const CATEGORIES = ['perkal', 'sk-lurah'];
    private const STATUSES = ['berlaku', 'dicabut'];

    public function index(): JsonResponse
    {
        $items = LegalProduct::query()->orderByDesc('year')->orderBy('title')->get()->map(fn (LegalProduct $item) => $this->present($item));
        return response()->json(['success' => true, 'message' => 'Data produk hukum berhasil diambil.', 'data' => $items]);
    }

    public function show(int $legal_product_id): JsonResponse
    {
        $item = LegalProduct::query()->findOrFail($legal_product_id);
        return response()->json(['success' => true, 'message' => 'Detail produk hukum berhasil diambil.', 'data' => $this->present($item)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $item = $this->persist($request, $validated, new LegalProduct());
        return response()->json(['success' => true, 'message' => 'Produk hukum berhasil dibuat.', 'data' => $this->present($item)], 201);
    }

    public function update(Request $request, int $legal_product_id): JsonResponse
    {
        $item = LegalProduct::query()->findOrFail($legal_product_id);
        $validated = $request->validate($this->rules(true));
        $item = $this->persist($request, $validated, $item);
        return response()->json(['success' => true, 'message' => 'Produk hukum berhasil diperbarui.', 'data' => $this->present($item)]);
    }

    public function destroy(int $legal_product_id): JsonResponse
    {
        $item = LegalProduct::query()->findOrFail($legal_product_id);
        $this->deleteFile($item->getRawOriginal('document'));
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Produk hukum berhasil dihapus.']);
    }

    public function download(int $legal_product_id): StreamedResponse
    {
        $item = LegalProduct::query()->findOrFail($legal_product_id);
        $path = $this->storagePath($item->getRawOriginal('document'));
        abort_unless($path && Storage::disk('public')->exists($path), 404, 'Dokumen tidak ditemukan.');
        return Storage::disk('public')->download($path, (Str::slug($item->title) ?: 'produk-hukum') . '.pdf');
    }

    private function rules(bool $partial = false): array
    {
        $required = fn (array $rules) => $partial ? array_merge(['sometimes'], $rules) : $rules;
        return [
            'title' => $required(['required', 'string', 'max:255']), 'category' => $required(['required', Rule::in(self::CATEGORIES)]),
            'status' => ['nullable', Rule::in(self::STATUSES)], 'number' => ['nullable', 'string', 'max:100'],
            'year' => $required(['required', 'integer', 'between:1945,2100']), 'description' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    private function persist(Request $request, array $validated, LegalProduct $item): LegalProduct
    {
        $data = collect($validated)->except(['document'])->all();
        $data['status'] = $data['status'] ?? $item->status ?? 'berlaku';
        if (!$item->exists) $data['created_by'] = $request->user()?->user_id;
        if ($request->hasFile('document')) {
            $this->deleteFile($item->getRawOriginal('document'));
            $data['document'] = $request->file('document')->store('legal-products', 'public');
        }
        $item->fill($data)->save();
        return $item->fresh();
    }

    private function present(LegalProduct $item): array
    {
        return ['legal_product_id' => $item->legal_product_id, 'title' => $item->title, 'category' => $item->category, 'status' => $item->status,
            'number' => $item->number, 'year' => $item->year, 'description' => $item->description,
            'document' => $this->mediaUrl($item->getRawOriginal('document')), 'created_at' => $item->created_at];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        $url = Storage::disk('public')->url($path);
        return str_starts_with($url, '/') ? rtrim((string) config('app.url'), '/') . $url : $url;
    }

    private function storagePath(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) $path = parse_url($path, PHP_URL_PATH);
        return ltrim(str_replace('/storage/', '', (string) $path), '/');
    }

    private function deleteFile(?string $path): void
    {
        $path = $this->storagePath($path);
        if ($path) Storage::disk('public')->delete($path);
    }
}
