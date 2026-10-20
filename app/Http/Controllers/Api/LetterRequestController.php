<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\NotificationAvailable;
use App\Models\LetterNumberSequence;
use App\Models\LetterRequest;
use App\Models\LetterRequestAttachment;
use App\Models\LetterRequestStatusHistory;
use App\Models\LetterType;
use App\Models\Staff;
use App\Services\DeathTemplateService;
use App\Services\LetterPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LetterRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $letterRequests = LetterRequest::query()
        ->select([
            'letter_request_id',
            'request_code',
            'applicant_name',
            'applicant_nik',
            'applicant_phone',
            'applicant_address',
            // Read form_data only to derive the list-safe PNP identity below;
            // it is hidden again before the collection is serialized.
            'form_data',
            'letter_type_id',
            'status',
            'signature_type',
            'letter_number',
            'verified_by',
            'authorized_by_signer_id',
            'source',
            'notes',
            'submitted_at',
            'verified_at',
            'authorized_at',
        ])
        ->with([
            'letterType:letter_type_id,code,letter_name,category,blade_view,signer_id',
            'letterType.signer:staff_id,name,position',
            'verifier:user_id,full_name',
            'authorizedSigner:staff_id,name,position',
        ])
        ->latest('submitted_at')
        ->get()
        ->map(function (LetterRequest $letterRequest): LetterRequest {
            if (strtoupper((string) $letterRequest->letterType?->code) === 'PNP') {
                $form = (array) ($letterRequest->form_data ?? []);
                $pick = static function (array $keys) use ($form): ?string {
                    foreach ($keys as $key) {
                        $value = $form[$key] ?? null;
                        if (is_scalar($value) && trim((string) $value) !== '') {
                            return (string) $value;
                        }
                    }

                    return null;
                };

                // List API does not expose complete form_data. Return only the
                // bride identity needed by admin tables, preserving stored
                // applicant_* values and keeping the rest of the form private.
                $letterRequest->setAttribute('applicant_display', [
                    'name' => $pick(['bride_name', 'brideName', 'nama_catin_putri', 'nama_mempelai_wanita']),
                    'nik' => $pick(['bride_nik', 'brideNik', 'nik_catin_putri', 'nik_mempelai_wanita']),
                    'address' => $pick(['bride_address', 'brideAddress', 'alamat_catin_putri', 'alamat_mempelai_wanita']),
                ]);
            }

            $letterRequest->makeHidden('form_data');

            return $letterRequest;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data permohonan surat berhasil diambil.',
            'data' => $letterRequests,
        ]);
    }

    // Dipanggil dari LetterCreateView.vue -- step 3 "Cetak Surat"
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'letter_type_id' => ['required', 'exists:letter_types,letter_type_id'],
        ]);
        $letterType = LetterType::findOrFail($request->input('letter_type_id'));
        $isResearchResponse = strtoupper((string) $letterType->code) === 'SBP';

        $validated = $request->validate([
            'letter_type_id' => ['required', 'exists:letter_types,letter_type_id'],
            'citizen_id' => ['nullable', 'exists:citizens,citizen_id'],
            'applicant_name' => ['required', 'string', 'max:100'],
            'applicant_nik' => $isResearchResponse
                ? ['nullable', 'string', 'size:16']
                : ['required', 'string', 'size:16'],
            'applicant_phone' => ['nullable', 'string', 'max:15'],
            'applicant_address' => $isResearchResponse
                ? ['nullable', 'string']
                : ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', Rule::in(['Online', 'Manual (Kelurahan)'])],
            'form_data' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.label' => ['nullable', 'string', 'max:225'],
            'attachments.*.file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        // applicant_nik is non-nullable in the existing table. SBP is an
        // institutional research reply and does not require resident identity.
        if ($isResearchResponse && ($validated['applicant_nik'] ?? null) === null) {
            $validated['applicant_nik'] = '';
        }
        $this->validateFormData(
            $letterType,
            $validated['form_data'] ?? [],
            ($validated['source'] ?? 'Manual (Kelurahan)') === 'Manual (Kelurahan)',
        );

        // signature_type ikut default dari letter_type, bisa diubah lagi saat otorisasi
        $validated['signature_type'] = $letterType->signature_method === 'digital' ? 'digital' : 'manual';
        $validated['source'] = $validated['source'] ?? 'Manual (Kelurahan)';

        $uploadedPaths = [];
        try {
            $letterRequest = DB::transaction(function () use ($validated, $request, $letterType, &$uploadedPaths): LetterRequest {
                $letterRequest = LetterRequest::create($validated);
                $documents = $letterType->documents()->get();

                $attachmentLabels = $request->input('attachments', []);
                foreach ($request->file('attachments', []) as $index => $attachment) {
                    $file = $attachment['file'] ?? null;
                    if (! $file) {
                        continue;
                    }

                    $document = $this->matchAttachmentDocument(
                        (string) data_get($attachmentLabels, "{$index}.label", ''),
                        $documents,
                    );

                    if (! $document) {
                        throw ValidationException::withMessages([
                            'attachments' => 'Jenis dokumen lampiran tidak dapat dicocokkan dengan tipe surat.',
                        ]);
                    }

                    $path = $file->store("letter-requests/{$letterRequest->letter_request_id}", 'public');
                    $uploadedPaths[] = $path;

                    LetterRequestAttachment::create([
                        'letter_request_id' => $letterRequest->letter_request_id,
                        'letter_type_document_id' => $document->letter_type_document_id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => Storage::disk('public')->url($path),
                        'uploaded_at' => now(),
                    ]);
                }

                return $letterRequest;
            });
        } catch (\Throwable $exception) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        $letterRequest->load(['citizen', 'letterType.signer', 'attachments.letterTypeDocument']);

        if ($letterRequest->source === 'Online') {
            event(new NotificationAvailable(
                'letter',
                (int) $letterRequest->letter_request_id,
                'created',
                now()->toISOString(),
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dibuat.',
            'data' => $letterRequest,
        ], 201);
    }

    /** Match admin upload labels to this letter type's configured document rows. */
    private function matchAttachmentDocument(string $label, $documents)
    {
        $normalize = static fn (string $value): string => preg_replace('/[^a-z0-9]/', '', mb_strtolower($value)) ?? '';
        $needle = $normalize($label);

        foreach ($documents as $document) {
            if ($needle !== '' && $normalize((string) $document->document_name) === $needle) {
                return $document;
            }
        }

        $ignoredWords = ['fotokopi', 'fotocopy', 'foto', 'surat', 'dokumen', 'orang', 'orangtua', 'dan', 'yang', 'untuk', 'dari', 'bagi', 'dua', 'el'];
        $words = static fn (string $value): array => preg_split('/[^a-z0-9]+/', mb_strtolower($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $labelWords = array_values(array_diff(array_filter($words($label), fn (string $word): bool => strlen($word) > 2), $ignoredWords));
        $bestDocument = null;
        $bestScore = 0;

        foreach ($documents as $document) {
            $documentWords = array_values(array_diff(array_filter($words((string) $document->document_name), fn (string $word): bool => strlen($word) > 2), $ignoredWords));
            $score = count(array_intersect($labelWords, $documentWords));
            if ($score > $bestScore) {
                $bestDocument = $document;
                $bestScore = $score;
            }
        }
        if ($bestScore > 0) {
            return $bestDocument;
        }

        return $documents->first(fn ($document) => ! $document->is_required
            && preg_match('/lain|pendukung|tambahan/i', (string) $document->document_name));
    }

    public function show(int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::query()
            ->with([
                'letterType.signer',
                'verifier',
                'authorizedSigner',
                'attachments.letterTypeDocument',
            ])
            ->findOrFail($letterRequest_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail permohonan surat berhasil diambil.',
            'data' => $letterRequest,
        ]);
    }

    public function pdf(
        Request $request,
        int $letterRequest_id,
        LetterPdfService $pdfService,
        DeathTemplateService $deathTemplateService,
    )
    {
        $letterRequest = LetterRequest::query()
            ->with(['citizen', 'letterType.signer', 'verifier', 'authorizedSigner'])
            ->findOrFail($letterRequest_id);

        if ($request->filled('signer_id')) {
            $signer = Staff::find($request->input('signer_id'));
            if ($signer) {
                $letterRequest->setRelation('authorizedSigner', $signer);
            }
        }

        $code = strtoupper((string) ($letterRequest->letterType?->code ?? ''));
        $bladeView = (string) ($letterRequest->letterType?->blade_view ?? '');

        if ($code === 'SKKM' || str_starts_with($bladeView, 'letters.death.')) {
            $deathTemplate = $code === 'SKKM'
                ? 'death-certificate'
                : str($bladeView)->afterLast('.')->toString();

            if (! $deathTemplateService->hasTemplate($deathTemplate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template kematian untuk tipe surat ini belum tersedia.',
                ], 422);
            }

            $pdf = $deathTemplateService->pdf(
                $deathTemplate,
                $deathTemplateService->dataForRequest($letterRequest, $deathTemplate),
            );

            $filename = ($letterRequest->request_code ?: 'surat') . '.pdf';

            return $request->boolean('download')
                ? $pdf->download($filename)
                : $pdf->stream($filename);
        }

        $template = $pdfService->resolveTemplate($letterRequest);

        if (! $template) {
            return response()->json([
                'success' => false,
                'message' => 'Template PDF untuk tipe surat ini belum tersedia.',
            ], 422);
        }

        $pdf = $pdfService->pdf(
            $pdfService->viewName($template),
            $pdfService->viewData($letterRequest, $template),
        );

        $filename = ($letterRequest->request_code ?: 'surat') . '.pdf';

        return $request->boolean('download')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }

    public function update(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'applicant_name' => ['sometimes', 'required', 'string', 'max:100'],
            'applicant_phone' => ['nullable', 'string', 'max:15'],
            'applicant_address' => ['sometimes', 'required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $letterRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil diperbarui.',
            'data' => $letterRequest,
        ]);
    }

    public function destroy(int $letterRequest_id): JsonResponse
    {
        LetterRequest::findOrFail($letterRequest_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dihapus.',
        ]);
    }

    // ===== Halaman Verifikasi Surat (LetterVerificationView.vue) =====
    // Tombol "Setujui Permohonan" -> status jadi Diverifikasi
    // Tombol "Tolak Permohonan"   -> status jadi Ditolak
    public function verify(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'rejected'])],
            'notes' => ['nullable', 'string'],
        ]);

        if ($letterRequest->status !== 'submitted') {
            throw ValidationException::withMessages([
                'status' => 'Permohonan hanya dapat diverifikasi saat berstatus submitted.',
            ]);
        }

        $verifiedBy = $request->user()->user_id;

        $letterRequest->update([
            'status' => $validated['status'],
            'verified_by' => $verifiedBy,
            'notes' => $validated['notes'] ?? $letterRequest->notes,
            'verified_at' => now(),
        ]);

        LetterRequestStatusHistory::create([
            'letter_request_id' => $letterRequest->letter_request_id,
            'status' => $validated['status'],
            'note' => $validated['notes'] ?? null,
            'change_by' => $verifiedBy,
        ]);

        event(new NotificationAvailable(
            'letter',
            (int) $letterRequest->letter_request_id,
            'updated',
            now()->toISOString(),
        ));

        $letterRequest->load([
            'citizen',
            'letterType.signer',
            'verifier',
            'authorizedSigner',
            'attachments.letterTypeDocument',
            'statusHistories',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi permohonan surat berhasil disimpan.',
            'data' => $letterRequest,
        ]);
    }

    // ===== Halaman Verifikasi+Otorisasi Surat (digabung) =====
    // Pilih penandatangan + jenis TTD, keputusan: authorized / rejected
    public function authorize(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            // FIX #1: tambah 'rejected' sebagai opsi valid
            'status'                 => ['required', Rule::in(['authorized', 'rejected'])],
            'notes'                  => ['nullable', 'string'],
            // FIX #2: hapus 'nullable' agar required_if benar-benar enforce
            'authorized_by_signer_id' => ['required_if:status,authorized', 'exists:staff,staff_id'],
            'signature_type'          => ['required_if:status,authorized', Rule::in(['digital', 'manual'])],
        ]);

        if ($letterRequest->status !== 'verified') {
            throw ValidationException::withMessages([
                'status' => 'Permohonan hanya dapat diotorisasi saat berstatus verified.',
            ]);
        }

        // notes hanya relevan sebagai alasan penolakan.
        // Saat authorized, kolom notes TIDAK disentuh agar catatan verifikasi tidak tertimpa.
        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'authorized') {
            $updateData['authorized_by_signer_id'] = $validated['authorized_by_signer_id'];
            $updateData['signature_type']           = $validated['signature_type'];
            $updateData['authorized_at']            = now();
            // FIX #3: gunakan LetterNumberSequence + DB transaction (atomic)
            $updateData['letter_number']            = $this->generateLetterNumber($letterRequest->letterType);
        } elseif ($validated['status'] === 'rejected') {
            // Simpan alasan penolakan; fallback ke catatan sebelumnya jika kosong.
            $updateData['notes'] = $validated['notes'] ?? $letterRequest->notes;
        }

        $letterRequest->update($updateData);

        LetterRequestStatusHistory::create([
            'letter_request_id' => $letterRequest->letter_request_id,
            'status'            => $validated['status'],
            'note'              => $validated['notes'] ?? null,
            'change_by'         => $request->user()->user_id ?? null,
            'changed_at'        => now(),
        ]);

        event(new NotificationAvailable(
            'letter',
            (int) $letterRequest->letter_request_id,
            'updated',
            now()->toISOString(),
        ));

        $letterRequest->load([
            'citizen',
            'letterType.signer',
            'verifier',
            'authorizedSigner',
            'attachments.letterTypeDocument',
            'statusHistories',
        ]);

        return response()->json([
            'success' => true,
            'message' => $validated['status'] === 'authorized'
                ? 'Otorisasi permohonan surat berhasil disimpan.'
                : 'Permohonan surat berhasil ditolak.',
            'data' => $letterRequest,
        ]);
    }

    /**
     * Generate nomor surat secara atomic menggunakan tabel letter_number_sequences.
     * Memakai DB::transaction + lockForUpdate() untuk mencegah race condition
     * saat dua permohonan diotorisasi bersamaan (nomor surat tidak akan duplikat).
     */
    private function toRomanMonth(int $month): string
    {
        return match ($month) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => (string) $month,
        };
    }

    private function generateLetterNumber(LetterType $letterType): string
    {
        return DB::transaction(function () use ($letterType) {
            $now = now();
            $seq = LetterNumberSequence::lockForUpdate()->firstOrCreate(
                [
                    'letter_type_id' => $letterType->letter_type_id,
                    'year'           => $now->year,
                    'month'          => $now->month,
                ],
                ['last_sequence' => 0],
            );

            $seq->increment('last_sequence');
            $seq->refresh();

            $code       = rtrim($letterType->number_prefix ?: $letterType->code, '/');
            $sequence   = str_pad($seq->last_sequence, 3, '0', STR_PAD_LEFT);
            $monthRoman = $this->toRomanMonth($now->month);
            $year       = $now->year;

            return "{$code}/{$sequence}/{$monthRoman}/{$year}";
        });
    }

    // ===== Riwayat status =====
    public function statusHistories(int $letterRequest_id): JsonResponse
    {
        $histories = LetterRequestStatusHistory::where('letter_request_id', $letterRequest_id)
            ->latest('changed_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status surat berhasil diambil.',
            'data' => $histories,
        ]);
    }

    // ===== Lampiran dokumen =====
    public function storeAttachment(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'letter_type_document_id' => [
                'required',
                Rule::exists('letter_type_documents', 'letter_type_document_id')
                    ->where(fn ($query) => $query->where('letter_type_id', $letterRequest->letter_type_id)),
            ],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $storedPath = $request->file('file')->store("letter-requests/{$letterRequest_id}", 'public');

        $attachment = LetterRequestAttachment::create([
            'letter_request_id' => $letterRequest_id,
            'letter_type_document_id' => $validated['letter_type_document_id'],
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => Storage::disk('public')->url($storedPath),
            'uploaded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil diunggah.',
            'data' => $attachment->load('letterTypeDocument'),
        ], 201);
    }

    public function attachments(int $letterRequest_id): JsonResponse
    {
        $attachments = LetterRequestAttachment::with('letterTypeDocument')
            ->where('letter_request_id', $letterRequest_id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data lampiran berhasil diambil.',
            'data' => $attachments,
        ]);
    }

    private function validateFormData(LetterType $letterType, array $formData, bool $allowLegacyKeys = false): void
    {
        $fields = $letterType->fields()->get();
        $allowedKeys = $fields->pluck('field_key')->all();
        $unknownKeys = array_diff(array_keys($formData), $allowedKeys);

        if ($unknownKeys !== [] && ! $allowLegacyKeys) {
            throw ValidationException::withMessages([
                'form_data' => 'Terdapat field yang tidak terdaftar pada tipe surat: ' . implode(', ', $unknownKeys),
            ]);
        }

        $errors = [];
        foreach ($fields as $field) {
            $value = $formData[$field->field_key] ?? null;
            // Form admin memiliki kontrak field lama/camelCase yang dinormalisasi
            // saat render PDF. Validasi kelengkapan tetap dilakukan oleh wizard
            // admin; jangan menolak request hanya karena key canonical lama tidak
            // ada di metadata tipe surat.
            if ($field->is_required && ($value === null || $value === '') && ! $allowLegacyKeys) {
                $errors["form_data.{$field->field_key}"] = "Field {$field->field_label} wajib diisi.";
                continue;
            }

            if ($field->field_type === 'select' && $value !== null && $value !== '') {
                $options = array_map('strval', $field->options ?? []);
                if (!in_array((string) $value, $options, true)) {
                    $errors["form_data.{$field->field_key}"] = "Nilai field {$field->field_label} tidak valid.";
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
