<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\NotificationAvailable;
use App\Models\Citizen;
use App\Models\LetterClassificationSequence;
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
            'form_data',
        ])
        ->with([
            'letterType:letter_type_id,letter_name,category,blade_view,signer_id',
            'letterType.signer:staff_id,name,position',
            'verifier:user_id,full_name',
            'authorizedSigner:staff_id,name,position',
        ])
        ->latest('submitted_at')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data permohonan surat berhasil diambil.',
            'data' => $letterRequests,
        ]);
    }

    // Dipanggil dari LetterCreateView.vue -- step 3 "Cetak Surat"
    public function store(Request $request): JsonResponse
    {
        $isOnline = $request->input('source', 'Manual (Kelurahan)') === 'Online';

        $validated = $request->validate([
            'letter_type_id' => ['required', 'exists:letter_types,letter_type_id'],
            'citizen_id' => ['nullable', 'exists:citizens,citizen_id'],
            'applicant_name' => [$isOnline ? 'required' : 'nullable', 'string', 'max:100'],
            'applicant_nik' => [$isOnline ? 'required' : 'nullable', 'string', 'size:16'],
            'applicant_phone' => ['nullable', 'string', 'max:15'],
            'applicant_address' => [$isOnline ? 'required' : 'nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', Rule::in(['Online', 'Manual (Kelurahan)'])],
            'form_data' => ['nullable', 'array'],
            'is_new_resident' => ['nullable', 'boolean'],
        ]);

        $letterType = LetterType::findOrFail($validated['letter_type_id']);
        $validated['form_data'] = $this->normalizeGenderFormData(
            $this->normalizeLegacyMarriageStatus(
                $letterType,
                $validated['form_data'] ?? [],
            ),
        );
        $this->validateFormData(
            $letterType,
            $validated['form_data'] ?? [],
            ($validated['source'] ?? 'Manual (Kelurahan)') === 'Manual (Kelurahan)',
        );

        // signature_type ikut default dari letter_type, bisa diubah lagi saat otorisasi
        $validated['signature_type'] = $letterType->signature_method === 'digital' ? 'digital' : 'manual';
        $validated['source'] = $validated['source'] ?? 'Manual (Kelurahan)';
        $validated['is_new_resident'] = $request->has('is_new_resident')
            ? $request->boolean('is_new_resident')
            : (empty($validated['citizen_id']) && in_array(strtoupper((string) $letterType->code), ['FBWNI', 'BDK', 'SPKIAF', 'SKDT', 'SPKTP', 'SKTS', 'SPTTS', 'SPPWNI', 'SPPWF', 'SPTJP'], true));

        $letterRequest = LetterRequest::create($validated);
        $letterRequest->load(['citizen', 'letterType.signer']);

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

    /**
     * Gabungkan beberapa surat menjadi satu file PDF.
     * Query param: ids (wajib) – daftar letter_request_id dipisah koma, mis. ?ids=1,2,3
     *              download (opsional, 0/1) – stream default, download bila 1.
     *
     * Setiap surat di-render satu per satu menggunakan template Blade + DomPDF,
     * kemudian halaman-halamannya digabung berurutan via FPDI (setasign/fpdi).
     */
    public function bundlePdf(
        Request $request,
        LetterPdfService $pdfService,
        DeathTemplateService $deathTemplateService,
    ) {
        $idsParam = $request->query('ids', '');
        $ids = array_filter(array_map('trim', explode(',', (string) $idsParam)));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Parameter ids wajib diisi.'], 422);
        }

        if (count($ids) > 20) {
            return response()->json(['success' => false, 'message' => 'Maksimal 20 surat dapat digabung sekaligus.'], 422);
        }

        // Render tiap surat ke file PDF sementara, kumpulkan path-nya.
        $tmpPaths = [];
        try {
            foreach ($ids as $id) {
                if (! ctype_digit((string) $id)) {
                    return response()->json(['success' => false, 'message' => "ID surat '$id' tidak valid."], 422);
                }

                $letterRequest = LetterRequest::query()
                    ->with(['citizen', 'letterType.signer', 'verifier', 'authorizedSigner'])
                    ->find((int) $id);

                if (! $letterRequest) {
                    return response()->json(['success' => false, 'message' => "Surat dengan ID $id tidak ditemukan."], 404);
                }

                // Render satu surat ke tmp
                $code      = strtoupper((string) ($letterRequest->letterType?->code ?? ''));
                $bladeView = (string) ($letterRequest->letterType?->blade_view ?? '');

                if ($code === 'SKKM' || str_starts_with($bladeView, 'letters.death.')) {
                    $deathTemplate = $code === 'SKKM'
                        ? 'death-certificate'
                        : str($bladeView)->afterLast('.')->toString();

                    if (! $deathTemplateService->hasTemplate($deathTemplate)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Template kematian surat #$id belum tersedia.",
                        ], 422);
                    }

                    $renderedPdf = $deathTemplateService->pdf(
                        $deathTemplate,
                        $deathTemplateService->dataForRequest($letterRequest, $deathTemplate),
                    );
                } else {
                    $template = $pdfService->resolveTemplate($letterRequest);
                    if (! $template) {
                        return response()->json([
                            'success' => false,
                            'message' => "Template PDF surat #$id belum tersedia.",
                        ], 422);
                    }
                    $renderedPdf = $pdfService->pdf(
                        $pdfService->viewName($template),
                        $pdfService->viewData($letterRequest, $template),
                    );
                }

                // Simpan ke file tmp; nama unik agar aman bila request paralel
                $tmpPath = tempnam(sys_get_temp_dir(), "sibimo_pdf_{$id}_");
                file_put_contents($tmpPath, $renderedPdf->output());
                $tmpPaths[] = $tmpPath;
            }

            // Gabung semua halaman menggunakan FPDI
            $fpdi = new \setasign\Fpdi\Fpdi();

            foreach ($tmpPaths as $path) {
                $pageCount = $fpdi->setSourceFile($path);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tpl = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($tpl);
                    $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                    $fpdi->addPage($orientation, [$size['width'], $size['height']]);
                    $fpdi->useTemplate($tpl);
                }
            }

            $mergedContent = $fpdi->Output('S'); // S = return as string

            $customName = trim((string) $request->query('filename', ''));
            if ($customName !== '') {
                $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '-', $customName);
                if (! str_ends_with(strtolower($filename), '.pdf')) {
                    $filename .= '.pdf';
                }
            } else {
                $filename = 'paket-surat-' . now()->format('YmdHis') . '.pdf';
            }

            return $request->boolean('download')
                ? response($mergedContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ])
                : response($mergedContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]);
        } finally {
            // Selalu bersihkan file tmp meski terjadi error
            foreach ($tmpPaths as $path) {
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }
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

        // Data lama bisa memakai label status "Pending"/"Menunggu".
        // Perlakukan semuanya sebagai status submitted agar tetap bisa
        // diverifikasi, tetapi jangan membuka status yang sudah diproses.
        $pendingStatuses = ['submitted', 'pending', 'menunggu', 'diajukan'];
        if (! in_array(strtolower(trim((string) $letterRequest->status)), $pendingStatuses, true)) {
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

        // Surat keterangan tanah diterbitkan langsung oleh Bu Lurah. Jangan
        // biarkan pilihan penandatangan di layar otorisasi mengganti ketentuan
        // ini menjadi pejabat lain.
        if ($validated['status'] === 'authorized'
            && in_array(strtoupper((string) $letterRequest->letterType?->code), ['SKTA', 'SKTH'], true)) {
            $signer = Staff::query()->find($validated['authorized_by_signer_id']);
            $position = mb_strtolower(trim((string) $signer?->position));
            $isLurah = (str_starts_with($position, 'lurah') || str_contains($position, 'kepala desa'))
                && !str_contains($position, 'urusan')
                && !str_starts_with($position, 'kaur');

            if (!$isLurah) {
                throw ValidationException::withMessages([
                    'authorized_by_signer_id' => 'Surat keterangan tanah wajib ditandatangani Bu Lurah.',
                ]);
            }
        }

        // notes hanya relevan sebagai alasan penolakan.
        // Saat authorized, kolom notes TIDAK disentuh agar catatan verifikasi tidak tertimpa.
        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'authorized') {
            $updateData['authorized_by_signer_id'] = $validated['authorized_by_signer_id'];
            $updateData['signature_type']           = $validated['signature_type'];
            $updateData['authorized_at']            = now();
            // Gunakan sequence berdasarkan indeks klasifikasi + DB transaction.
            $updateData['letter_number']            = $this->generateLetterNumber($letterRequest->letterType);
        } elseif ($validated['status'] === 'rejected') {
            // Simpan alasan penolakan; fallback ke catatan sebelumnya jika kosong.
            $updateData['notes'] = $validated['notes'] ?? $letterRequest->notes;
        }

        $letterRequest->update($updateData);

        // Jika permohonan merupakan pendaftaran warga baru (penduduk tetap) dan
        // diotorisasi, buat catatan warga baru di tabel citizens agar NIK-nya
        // langsung terdaftar dan bisa dicek pada alur berikutnya.
        if ($validated['status'] === 'authorized'
            && ($letterRequest->is_new_resident || in_array(strtoupper((string) $letterRequest->letterType?->code), ['FBWNI', 'BDK', 'SPKIAF', 'SKDT'], true))) {
            $this->registerNewResidentCitizen($letterRequest);
        }

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
     * Generate nomor surat secara atomic berdasarkan indeks klasifikasi.
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
            $classificationIndex = trim((string) ($letterType->classification_index ?: '100.2'));
            $seq = LetterClassificationSequence::lockForUpdate()->firstOrCreate(
                [
                    'classification_index' => $classificationIndex,
                    'year' => $now->year,
                ],
                ['last_sequence' => 0],
            );

            $seq->increment('last_sequence');
            $seq->refresh();

            $sequence   = str_pad($seq->last_sequence, 3, '0', STR_PAD_LEFT);

            return "{$classificationIndex}/{$sequence}";
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

    /**
     * PNP pernah dikirim oleh beberapa versi admin dengan key camelCase atau
     * dengan status perkawinan umum. Normalisasi di backend mencegah status
     * catin pria terbaca sebagai status catin putri ketika mapper lama masih
     * tersimpan di browser/cache.
     */
    private function normalizeLegacyMarriageStatus(LetterType $letterType, array $formData): array
    {
        if (strtoupper((string) $letterType->code) !== 'PNP') {
            return $formData;
        }

        $pick = static function (array $keys) use ($formData): mixed {
            foreach ($keys as $key) {
                $value = $formData[$key] ?? null;
                if ($value !== null && trim((string) $value) !== '') {
                    return $value;
                }
            }

            return null;
        };

        $normalize = static function (mixed $value, array $aliases): mixed {
            $value = trim((string) $value);
            $normalized = strtolower($value);

            return $aliases[$normalized] ?? $value;
        };

        $groomStatus = $pick(['groomStatus', 'groom_status', 'status_catin_putra']);
        if ($groomStatus !== null) {
            $formData['status_catin_putra'] = $normalize($groomStatus, [
                'perjaka' => 'Jejaka',
                'jejaka' => 'Jejaka',
                'belum kawin' => 'Jejaka',
                'belum menikah' => 'Jejaka',
                'duda' => 'Duda',
                'cerai hidup' => 'Duda',
                'cerai mati' => 'Duda',
                'kawin' => 'Beristri',
                'kawin tercatat' => 'Beristri',
                'kawin belum tercatat' => 'Beristri',
                'beristri' => 'Beristri',
            ]);
        }

        $brideStatus = $pick(['brideStatus', 'bride_status', 'status_catin_putri']);
        if ($brideStatus !== null) {
            $formData['status_catin_putri'] = $normalize($brideStatus, [
                'perawan' => 'Perawan',
                'belum kawin' => 'Perawan',
                'belum menikah' => 'Perawan',
                'janda' => 'Janda',
                'cerai hidup' => 'Janda',
                'cerai mati' => 'Janda',
            ]);
        }

        return $formData;
    }

    /**
     * Form admin/public dapat menampilkan jenis kelamin dengan nomor ("1.
     * Laki-laki" / "2. Perempuan"), sedangkan opsi metadata memakai nilai
     * database tanpa nomor. Normalisasi rekursif menjaga field pasangan, anak,
     * dan baris berulang tetap valid.
     */
    private function normalizeGenderFormData(array $formData): array
    {
        foreach ($formData as $key => $value) {
            if (is_array($value)) {
                $formData[$key] = $this->normalizeGenderFormData($value);
                continue;
            }

            if (! is_string($key) || ! preg_match('/(?:gender|jenis[_-]?kelamin)/i', $key) || ! is_string($value)) {
                continue;
            }

            $bare = trim((string) preg_replace('/^\s*\d+\s*[.)\-:]\s*/', '', $value));
            if (strcasecmp($bare, 'laki-laki') === 0) {
                $formData[$key] = 'Laki-laki';
            } elseif (strcasecmp($bare, 'perempuan') === 0) {
                $formData[$key] = 'Perempuan';
            }
        }

        return $formData;
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

    /**
     * Daftarkan pemohon dan seluruh anggota keluarga sebagai warga baru saat surat
     * pendaftaran penduduk tetap diotorisasi oleh admin.
     * Mengambil data dari applicant_*, form_data, dan form_data['members'].
     * Menggunakan updateOrCreate agar jika NIK sudah ada (mis. data parsial),
     * datanya diperbarui secara lengkap sesuai isian surat yang diajukan.
     */
    private function registerNewResidentCitizen(LetterRequest $letterRequest): void
    {
        $form = is_array($letterRequest->form_data) ? $letterRequest->form_data : [];

        // 1. Ambil data bersama keluarga (Nomor KK, Alamat, RT, RW, Dusun, Telepon)
        $familyCardNumber = $this->pickFormValue($form, ['nomor_kk', 'family_card_number', 'kk', 'no_kk', 'kkNumber', 'familyCardNumber', 'kk_number']);
        $address = $this->pickFormValue($form, ['address', 'alamat']) ?: $letterRequest->applicant_address;
        $rtRaw = $this->pickFormValue($form, ['rt', 'rt_number']);
        $rwRaw = $this->pickFormValue($form, ['rw', 'rw_number']);
        $dusun = $this->pickFormValue($form, ['hamlet', 'dusun', 'padukuhan']);
        $phone = $this->pickFormValue($form, ['phone', 'telepon', 'whatsapp', 'phone_number']) ?: $letterRequest->applicant_phone;

        // Resolusi RT ID
        $rtId = $this->resolveRtId($rtRaw, $rwRaw, $dusun, $address);
        $rtNumber = $rtRaw ? (string) $rtRaw : null;
        if ($rtId && !$rtNumber) {
            $rtModel = \App\Models\Rt::find($rtId);
            $rtNumber = $rtModel?->number;
        }

        // 2. Cek apakah ada daftar anggota keluarga ('members', 'familyMembers', 'family_members', 'anggota_keluarga')
        $members = $this->extractFamilyMembers($form);
        $registeredNiks = [];

        if (!empty($members)) {
            foreach ($members as $member) {
                if (!is_array($member)) {
                    continue;
                }

                $memberNik = $this->pickFormValue($member, ['nik', 'national_id', 'no_ktp', 'applicant_nik']);
                $memberName = $this->pickFormValue($member, ['name', 'full_name', 'nama', 'nama_lengkap', 'applicant_name']);

                // Abaikan baris kosong tanpa NIK dan tanpa Nama
                if (!$memberNik && !$memberName) {
                    continue;
                }

                // Jika baris adalah pemohon tanpa NIK terisi di baris tabel, fallback ke applicant_nik
                if (!$memberNik && $memberName && strcasecmp(trim((string) $memberName), trim((string) $letterRequest->applicant_name)) === 0) {
                    $memberNik = $letterRequest->applicant_nik;
                }

                if (!$memberNik) {
                    continue;
                }

                $citizenData = $this->mapMemberToCitizenData(
                    $member,
                    $form,
                    $familyCardNumber ? (string) $familyCardNumber : null,
                    $address,
                    $rtId,
                    $rtNumber,
                    $phone,
                    $letterRequest
                );

                $citizen = Citizen::updateOrCreate(
                    ['national_id' => $memberNik],
                    $citizenData
                );

                $registeredNiks[] = $memberNik;

                // Jika baris ini cocok dengan pemohon surat, kaitkan citizen_id ke surat
                if ($memberNik === $letterRequest->applicant_nik || (!$letterRequest->citizen_id && count($registeredNiks) === 1)) {
                    $letterRequest->update(['citizen_id' => $citizen->citizen_id]);
                }
            }
        }

        // 3. Jika pemohon (applicant_nik) belum terdaftar (mis. bukan anggota di tabel members atau form tunggal)
        $applicantNik = $letterRequest->applicant_nik;
        if ($applicantNik && !in_array($applicantNik, $registeredNiks, true)) {
            $applicantData = $this->mapSingleApplicantToCitizenData(
                $form,
                $familyCardNumber ? (string) $familyCardNumber : null,
                $address,
                $rtId,
                $rtNumber,
                $phone,
                $letterRequest
            );

            $citizen = Citizen::updateOrCreate(
                ['national_id' => $applicantNik],
                $applicantData
            );

            if (!$letterRequest->citizen_id) {
                $letterRequest->update(['citizen_id' => $citizen->citizen_id]);
            }
        }
    }

    private function mapMemberToCitizenData(
        array $member,
        array $form,
        ?string $familyCardNumber,
        ?string $address,
        ?int $rtId,
        ?string $rtNumber,
        ?string $phone,
        LetterRequest $letterRequest
    ): array {
        $nik = $this->pickFormValue($member, ['nik', 'national_id', 'no_ktp'])
            ?: ($letterRequest->applicant_nik ?: null);
        $name = $this->pickFormValue($member, ['name', 'full_name', 'nama', 'nama_lengkap'])
            ?: ($nik === $letterRequest->applicant_nik ? $letterRequest->applicant_name : null);

        $gender = $this->decodeGender($this->pickFormValue($member, ['gender', 'jenis_kelamin']));
        $birthPlace = $this->pickFormValue($member, ['birthPlace', 'birth_place', 'tempat_lahir']);
        $birthDate = $this->formatDateValue($this->pickFormValue($member, ['birthDate', 'birth_date', 'tanggal_lahir']));

        $bloodType = $this->decodeBloodType($this->pickFormValue($member, ['bloodType', 'blood_type', 'golongan_darah']));
        $religion = $this->decodeReligion($this->pickFormValue($member, ['religion', 'agama']));
        $maritalStatus = $this->decodeMaritalStatus($this->pickFormValue($member, ['maritalStatus', 'marital_status', 'status_perkawinan', 'status_kawin']));
        $familyRelationship = $this->decodeShdk($this->pickFormValue($member, ['shdk', 'family_relationship', 'hubungan_keluarga', 'status_hubungan']));
        $education = $this->decodeEducation($this->pickFormValue($member, ['education', 'pendidikan', 'pendidikan_terakhir']));
        $occupation = $this->decodeOccupation($this->pickFormValue($member, ['occupation', 'pekerjaan']));

        $birthCertStatus = $this->decodeCertificateStatus($this->pickFormValue($member, ['birthCertStatus', 'birth_cert_status', 'akta_lahir', 'birth_certificate_status']));
        $birthCertNumber = $this->pickFormValue($member, ['birthCertNumber', 'birth_cert_number', 'no_akta_lahir', 'birth_certificate_number']);

        $marriageCertStatus = $this->decodeCertificateStatus($this->pickFormValue($member, ['marriageCertStatus', 'marriage_cert_status', 'akta_kawin', 'marriage_certificate_status']));
        $marriageCertNumber = $this->pickFormValue($member, ['marriageCertNumber', 'marriage_cert_number', 'no_akta_nikah', 'marriage_certificate_number']);
        $marriageDate = $this->formatDateValue($this->pickFormValue($member, ['marriageDate', 'marriage_date', 'tanggal_perkawinan', 'tanggal_nikah']));

        $divorceCertStatus = $this->decodeCertificateStatus($this->pickFormValue($member, ['divorceCertStatus', 'divorce_cert_status', 'akta_cerai', 'divorce_certificate_status']));
        $divorceCertNumber = $this->pickFormValue($member, ['divorceCertNumber', 'divorce_cert_number', 'no_akta_cerai', 'divorce_certificate_number']);
        $divorceDate = $this->formatDateValue($this->pickFormValue($member, ['divorceDate', 'divorce_date', 'tanggal_perceraian']));

        $disabilityStatus = $this->decodeCertificateStatus($this->pickFormValue($member, ['disorder', 'kelainan_fisik', 'disability_status']));
        $disabilityType = $this->decodeDisabilityType($this->pickFormValue($member, ['disability', 'penyandang_cacat', 'disability_type']));

        $fatherNik = $this->pickFormValue($member, ['fatherNik', 'father_nik', 'nik_ayah', 'father_national_id']);
        $fatherName = $this->pickFormValue($member, ['fatherName', 'father_name', 'nama_ayah']);
        $motherNik = $this->pickFormValue($member, ['motherNik', 'mother_nik', 'nik_ibu', 'mother_national_id']);
        $motherName = $this->pickFormValue($member, ['motherName', 'mother_name', 'nama_ibu']);

        $previousAddress = $this->pickFormValue($member, ['previousAddress', 'previous_address', 'alamat_sebelumnya'])
            ?: $this->pickFormValue($form, ['previousAddress', 'previous_address', 'alamat_sebelumnya']);
        $passportNumber = $this->pickFormValue($member, ['passportNumber', 'passport_number', 'no_paspor']);
        $passportExpiry = $this->formatDateValue($this->pickFormValue($member, ['passportExpiry', 'passport_expiry_date', 'tgl_berakhir_paspor']));

        $memberPhone = $this->pickFormValue($member, ['phone', 'telepon', 'whatsapp', 'phone_number']);
        if (!$memberPhone && ($nik === $letterRequest->applicant_nik)) {
            $memberPhone = $phone;
        }

        return [
            'record_type'                 => 'PENDUDUK',
            'national_id'                 => $nik,
            'full_name'                   => $name,
            'family_card_number'          => $familyCardNumber,
            'birth_place'                 => $birthPlace ? (string) $birthPlace : null,
            'birth_date'                  => $birthDate,
            'gender'                      => $gender,
            'address'                     => $address,
            'ktp_address'                 => $address,
            'phone_number'                => $memberPhone ? (string) $memberPhone : null,
            'rt_id'                       => $rtId,
            'rt'                          => $rtNumber,
            'birth_certificate_status'     => $birthCertStatus,
            'birth_certificate_number'     => $birthCertNumber ? (string) $birthCertNumber : null,
            'blood_type'                  => $bloodType,
            'religion'                    => $religion,
            'occupation'                  => $occupation,
            'education'                   => $education,
            'marital_status'              => $maritalStatus,
            'marriage_certificate_status'  => $marriageCertStatus,
            'marriage_certificate_number'  => $marriageCertNumber ? (string) $marriageCertNumber : null,
            'marriage_date'               => $marriageDate,
            'divorce_certificate_status'   => $divorceCertStatus,
            'divorce_certificate_number'   => $divorceCertNumber ? (string) $divorceCertNumber : null,
            'divorce_date'                => $divorceDate,
            'family_relationship'          => $familyRelationship,
            'disability_status'            => $disabilityStatus,
            'disability_type'              => $disabilityType,
            'father_national_id'           => $fatherNik ? (string) $fatherNik : null,
            'father_name'                  => $fatherName ? (string) $fatherName : null,
            'mother_national_id'           => $motherNik ? (string) $motherNik : null,
            'mother_name'                  => $motherName ? (string) $motherName : null,
            'previous_address'             => $previousAddress ? (string) $previousAddress : null,
            'passport_number'              => $passportNumber ? (string) $passportNumber : null,
            'passport_expiry_date'         => $passportExpiry,
            'nationality'                  => 'WNI',
            'status'                      => 'Active',
        ];
    }

    private function mapSingleApplicantToCitizenData(
        array $form,
        ?string $familyCardNumber,
        ?string $address,
        ?int $rtId,
        ?string $rtNumber,
        ?string $phone,
        LetterRequest $letterRequest
    ): array {
        $nik = $letterRequest->applicant_nik ?: $this->pickFormValue($form, ['nik', 'national_id', 'applicant_nik']);
        $name = $letterRequest->applicant_name ?: $this->pickFormValue($form, ['name', 'full_name', 'nama', 'applicant_name']);

        $gender = $this->decodeGender($this->pickFormValue($form, ['gender', 'jenis_kelamin']));
        $birthPlace = $this->pickFormValue($form, ['birthPlace', 'birth_place', 'tempat_lahir']);
        $birthDate = $this->formatDateValue($this->pickFormValue($form, ['birthDate', 'birth_date', 'tanggal_lahir']));

        $bloodType = $this->decodeBloodType($this->pickFormValue($form, ['bloodType', 'blood_type', 'golongan_darah']));
        $religion = $this->decodeReligion($this->pickFormValue($form, ['religion', 'agama']));
        $maritalStatus = $this->decodeMaritalStatus($this->pickFormValue($form, ['maritalStatus', 'marital_status', 'status_perkawinan', 'status_kawin']));
        $familyRelationship = $this->decodeShdk($this->pickFormValue($form, ['familyRelationship', 'family_relationship', 'shdk', 'hubungan_keluarga'])) ?: 'Kepala Keluarga';
        $education = $this->decodeEducation($this->pickFormValue($form, ['education', 'pendidikan', 'pendidikan_terakhir']));
        $occupation = $this->decodeOccupation($this->pickFormValue($form, ['occupation', 'pekerjaan']));

        $birthCertStatus = $this->decodeCertificateStatus($this->pickFormValue($form, ['birthCertStatus', 'birth_cert_status', 'akta_lahir', 'birth_certificate_status']));
        $birthCertNumber = $this->pickFormValue($form, ['birthCertNumber', 'birth_cert_number', 'no_akta_lahir', 'birth_certificate_number']);

        $marriageCertStatus = $this->decodeCertificateStatus($this->pickFormValue($form, ['marriageCertStatus', 'marriage_cert_status', 'akta_kawin', 'marriage_certificate_status']));
        $marriageCertNumber = $this->pickFormValue($form, ['marriageCertNumber', 'marriage_cert_number', 'no_akta_nikah', 'marriage_certificate_number']);
        $marriageDate = $this->formatDateValue($this->pickFormValue($form, ['marriageDate', 'marriage_date', 'tanggal_perkawinan', 'tanggal_nikah']));

        $divorceCertStatus = $this->decodeCertificateStatus($this->pickFormValue($form, ['divorceCertStatus', 'divorce_cert_status', 'akta_cerai', 'divorce_certificate_status']));
        $divorceCertNumber = $this->pickFormValue($form, ['divorceCertNumber', 'divorce_cert_number', 'no_akta_cerai', 'divorce_certificate_number']);
        $divorceDate = $this->formatDateValue($this->pickFormValue($form, ['divorceDate', 'divorce_date', 'tanggal_perceraian']));

        $disabilityStatus = $this->decodeCertificateStatus($this->pickFormValue($form, ['disorder', 'kelainan_fisik', 'disability_status']));
        $disabilityType = $this->decodeDisabilityType($this->pickFormValue($form, ['disability', 'penyandang_cacat', 'disability_type']));

        $fatherNik = $this->pickFormValue($form, ['fatherNik', 'father_nik', 'nik_ayah', 'father_national_id']);
        $fatherName = $this->pickFormValue($form, ['fatherName', 'father_name', 'nama_ayah']);
        $motherNik = $this->pickFormValue($form, ['motherNik', 'mother_nik', 'nik_ibu', 'mother_national_id']);
        $motherName = $this->pickFormValue($form, ['motherName', 'mother_name', 'nama_ibu']);

        $previousAddress = $this->pickFormValue($form, ['previousAddress', 'previous_address', 'alamat_sebelumnya', 'originAddress', 'origin_address']);
        $passportNumber = $this->pickFormValue($form, ['passportNumber', 'passport_number', 'no_paspor']);
        $passportExpiry = $this->formatDateValue($this->pickFormValue($form, ['passportExpiry', 'passport_expiry_date', 'tgl_berakhir_paspor']));

        return [
            'record_type'                 => 'PENDUDUK',
            'national_id'                 => $nik,
            'full_name'                   => $name,
            'family_card_number'          => $familyCardNumber,
            'birth_place'                 => $birthPlace ? (string) $birthPlace : null,
            'birth_date'                  => $birthDate,
            'gender'                      => $gender,
            'address'                     => $address,
            'ktp_address'                 => $address,
            'phone_number'                => $phone ? (string) $phone : null,
            'rt_id'                       => $rtId,
            'rt'                          => $rtNumber,
            'birth_certificate_status'     => $birthCertStatus,
            'birth_certificate_number'     => $birthCertNumber ? (string) $birthCertNumber : null,
            'blood_type'                  => $bloodType,
            'religion'                    => $religion,
            'occupation'                  => $occupation,
            'education'                   => $education,
            'marital_status'              => $maritalStatus,
            'marriage_certificate_status'  => $marriageCertStatus,
            'marriage_certificate_number'  => $marriageCertNumber ? (string) $marriageCertNumber : null,
            'marriage_date'               => $marriageDate,
            'divorce_certificate_status'   => $divorceCertStatus,
            'divorce_certificate_number'   => $divorceCertNumber ? (string) $divorceCertNumber : null,
            'divorce_date'                => $divorceDate,
            'family_relationship'          => $familyRelationship,
            'disability_status'            => $disabilityStatus,
            'disability_type'              => $disabilityType,
            'father_national_id'           => $fatherNik ? (string) $fatherNik : null,
            'father_name'                  => $fatherName ? (string) $fatherName : null,
            'mother_national_id'           => $motherNik ? (string) $motherNik : null,
            'mother_name'                  => $motherName ? (string) $motherName : null,
            'previous_address'             => $previousAddress ? (string) $previousAddress : null,
            'passport_number'              => $passportNumber ? (string) $passportNumber : null,
            'passport_expiry_date'         => $passportExpiry,
            'nationality'                  => 'WNI',
            'status'                      => 'Active',
        ];
    }

    private function extractFamilyMembers(array $form): array
    {
        $members = $form['members'] ?? $form['familyMembers'] ?? $form['family_members'] ?? $form['anggota_keluarga'] ?? [];
        return is_array($members) ? $members : [];
    }

    private function resolveRtId(?string $rt, ?string $rw, ?string $dusun, ?string $address): ?int
    {
        $region = null;
        if ($dusun) {
            $region = \App\Models\Region::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($dusun))])->first();
        }
        if (!$region && $address) {
            $region = \App\Models\Region::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($address))])->first();
        }

        $rwModel = null;
        if ($rw) {
            $cleanRw = preg_replace('/^0+/', '', trim((string) $rw)) ?: '0';
            $rwQuery = \App\Models\Rw::query();
            if ($region) {
                $rwQuery->where('region_id', $region->region_id);
            }
            $rwModel = $rwQuery->get()->first(function ($item) use ($cleanRw) {
                return (preg_replace('/^0+/', '', trim((string) $item->number)) ?: '0') === $cleanRw;
            });
        }

        if ($rt) {
            $cleanRt = preg_replace('/^0+/', '', trim((string) $rt)) ?: '0';
            $rtQuery = \App\Models\Rt::query();
            if ($rwModel) {
                $rtQuery->where('rw_id', $rwModel->id);
            }
            $rtRecord = $rtQuery->get()->first(function ($item) use ($cleanRt) {
                return (preg_replace('/^0+/', '', trim((string) $item->number)) ?: '0') === $cleanRt;
            });
            if ($rtRecord) {
                return $rtRecord->id;
            }

            // Fallback: cari RT sembarang dengan nomor yang sama
            $fallbackRt = \App\Models\Rt::whereRaw('CAST(number AS UNSIGNED) = ?', [(int) $cleanRt])->first();
            return $fallbackRt?->id;
        }

        return null;
    }

    private function decodeGender(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        if ($val === '1' || str_starts_with($val, '1.') || stripos($val, 'laki') !== false) {
            return 'Laki-laki';
        }
        if ($val === '2' || str_starts_with($val, '2.') || stripos($val, 'perempuan') !== false) {
            return 'Perempuan';
        }
        return $val;
    }

    private function decodeMaritalStatus(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1' => 'Belum Kawin',
            '2' => 'Kawin',
            '3' => 'Cerai Hidup',
            '4' => 'Cerai Mati',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeReligion(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1' => 'Islam',
            '2' => 'Kristen',
            '3' => 'Katolik',
            '4' => 'Hindu',
            '5' => 'Buddha',
            '6' => 'Khonghucu',
            '7' => 'Kepercayaan terhadap Tuhan YME',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeBloodType(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1'  => 'A',
            '2'  => 'B',
            '3'  => 'AB',
            '4'  => 'O',
            '5'  => 'A+',
            '6'  => 'A-',
            '7'  => 'B+',
            '8'  => 'B-',
            '9'  => 'AB+',
            '10' => 'AB-',
            '11' => 'O+',
            '12' => 'O-',
            '13' => 'Tidak Tahu',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeShdk(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1'  => 'Kepala Keluarga',
            '2'  => 'Suami',
            '3'  => 'Istri',
            '4'  => 'Anak',
            '5'  => 'Menantu',
            '6'  => 'Cucu',
            '7'  => 'Orang Tua',
            '8'  => 'Mertua',
            '9'  => 'Famili Lain',
            '10' => 'Pembantu',
            '11' => 'Lainnya',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeCertificateStatus(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        if ($val === '1' || stripos($val, 'tidak') !== false) {
            return 'Tidak Ada';
        }
        if ($val === '2' || stripos($val, 'ada') !== false) {
            return 'Ada';
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeDisabilityType(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1' => 'Cacat Fisik',
            '2' => 'Cacat Netra/Buta',
            '3' => 'Cacat Rungu/Wicara',
            '4' => 'Cacat Mental/Jiwa',
            '5' => 'Cacat Fisik dan Mental',
            '6' => 'Cacat Lainnya',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeEducation(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1'  => 'Tidak/Belum Sekolah',
            '2'  => 'Belum Tamat SD/Sederajat',
            '3'  => 'Tamat SD/Sederajat',
            '4'  => 'SLTP/Sederajat',
            '5'  => 'SLTA/Sederajat',
            '6'  => 'Diploma I/II',
            '7'  => 'Akademi/Diploma III/S. Muda',
            '8'  => 'Diploma IV/Strata I',
            '9'  => 'Strata II',
            '10' => 'Strata III',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function decodeOccupation(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        $map = [
            '1' => 'Belum/Tidak Bekerja',
            '2' => 'Mengurus Rumah Tangga',
            '3' => 'Pelajar/Mahasiswa',
            '4' => 'Pensiunan',
            '5' => 'Pegawai Negeri Sipil (PNS)',
            '6' => 'Tentara Nasional Indonesia (TNI)',
            '7' => 'Kepolisian RI (POLRI)',
            '8' => 'Perdagangan',
            '9' => 'Petani/Pekebun',
            '10' => 'Peternak',
            '11' => 'Nelayan/Perikanan',
            '12' => 'Industri',
            '13' => 'Konstruksi',
            '14' => 'Transportasi',
            '15' => 'Karyawan Swasta',
            '16' => 'Karyawan BUMN',
            '17' => 'Karyawan BUMD',
            '18' => 'Karyawan Honorer',
            '19' => 'Buruh Harian Lepas',
            '20' => 'Buruh Tani/Perkebunan',
            '21' => 'Buruh Nelayan/Perikanan',
            '22' => 'Buruh Peternakan',
            '23' => 'Pembantu Rumah Tangga',
            '24' => 'Tukang Cukur',
            '25' => 'Tukang Listrik',
            '26' => 'Tukang Batu',
            '27' => 'Tukang Kayu',
            '28' => 'Tukang Sol Sepatu',
            '29' => 'Tukang Las/Pandai Besi',
            '30' => 'Tukang Jahit',
            '31' => 'Tukang Gigi',
            '32' => 'Penata Rias',
            '33' => 'Penata Busana',
            '34' => 'Penata Rambut',
            '35' => 'Mekanik',
            '36' => 'Seniman',
            '37' => 'Tabib',
            '38' => 'Paraji',
            '39' => 'Perancang Busana',
            '40' => 'Penterjemah',
            '41' => 'Imam Mesjid',
            '42' => 'Pendeta',
            '43' => 'Pastor',
            '44' => 'Wartawan',
            '45' => 'Ustadz/Mubaligh',
            '46' => 'Juru Masak',
            '47' => 'Promotor Acara',
            '48' => 'Anggota DPR-RI',
            '49' => 'Anggota DPD',
            '50' => 'Anggota BPK',
            '51' => 'Presiden',
            '52' => 'Wakil Presiden',
            '53' => 'Anggota Mahkamah Konstitusi',
            '54' => 'Anggota Kabinet/Kementerian',
            '55' => 'Duta Besar',
            '56' => 'Gubernur',
            '57' => 'Wakil Gubernur',
            '58' => 'Bupati',
            '59' => 'Wakil Bupati',
            '60' => 'Walikota',
            '61' => 'Wakil Walikota',
            '62' => 'Anggota DPRD Provinsi',
            '63' => 'Anggota DPRD Kabupaten/Kota',
            '64' => 'Dosen',
            '65' => 'Guru',
            '66' => 'Pilot',
            '67' => 'Pengacara',
            '68' => 'Notaris',
            '69' => 'Arsitek',
            '70' => 'Akuntan',
            '71' => 'Konsultan',
            '72' => 'Dokter',
            '73' => 'Bidan',
            '74' => 'Perawat',
            '75' => 'Apoteker',
            '76' => 'Psikiater/Psikolog',
            '77' => 'Penyiar Televisi',
            '78' => 'Penyiar Radio',
            '79' => 'Pelaut',
            '80' => 'Peneliti',
            '81' => 'Sopir',
            '82' => 'Pialang',
            '83' => 'Paranormal',
            '84' => 'Pedagang',
            '85' => 'Perangkat Desa',
            '86' => 'Kepala Desa',
            '87' => 'Biarawati',
            '88' => 'Wiraswasta',
            '89' => 'Lainnya',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        return preg_replace('/^\d+\s*[.)\-:]\s*/', '', $val);
    }

    private function formatDateValue(mixed $raw): ?string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $val = trim((string) $raw);
        try {
            return \Carbon\Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Ambil nilai pertama yang ditemukan dari daftar key dalam form_data.
     */
    private function pickFormValue(array $form, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = $form[$key] ?? null;
            if ($value !== null && trim((string) $value) !== '') {
                return $value;
            }
        }

        return null;
    }
}
