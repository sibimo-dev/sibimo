<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\NotificationAvailable;
use App\Models\Citizen;
use App\Models\CitizenDocument;
use App\Models\LetterRequest;
use App\Models\LetterRequestAttachment;
use App\Models\LetterType;
use App\Services\DeathTemplateService;
use App\Services\LetterPdfService;
use App\Support\LetterNumberFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicLetterController extends Controller
{
    /** @var list<string> */
    private const ONLINE_CODES = [
        'SGC', 'FBWNI', 'SRBBM', 'SPKIA', 'SPKTP', 'SPKIAF', 'SPPWF', 'SPPWNI', 'SKDT', 'SKTS', 'SPTTS',
        'SKWKA', 'SPBNI', 'SPTMDK', 'SKDPAK', 'SPDP', 'SPTJP',
        'SKU', 'SKD', 'SKK', 'SKUM', 'SKP', 'RL', 'SKCK', 'SKTM', 'SKTMS', 'SKJG', 'SKBK',
        'SPCCP', 'SKKMP', 'SKUMN', 'SPNIK', 'SPNIP', 'SKTNB', 'FPDN', 'SKBKN', 'SPBML', 'SPOT',
        'SKTA', 'SKTH', 'SPKLC', 'SKC',
        'SPU', 'SKDN',
        'SKKL', 'PAK', 'SKAK', 'SPAKL', 'LPKL', 'FPK', 'LK', 'PPKT', 'LKLD', 'SPTJMPSI', 'SPTJMDK',
        'DKA', 'SKKM', 'DKS', 'DKU', 'DKP', 'DKC', 'DKR', 'DKF',
        'SKWNP', 'SPTKP', 'SKWHP', 'SKNNP', 'N1P', 'N2P', 'N4P', 'N5P', 'N6P', 'PNP', 'SKBK2', 'SPBMLP',
    ];

    /**
     * Surat yang boleh dipakai dalam alur pendaftaran warga baru sebelum
     * NIK-nya tersimpan sebagai citizen aktif.
     *
     * @var list<string>
     */
    private const NEW_RESIDENT_CODES = [
        'SPKTP', 'SPKIAF', 'FBWNI', 'PAK', 'SKDT', 'SKTS', 'SPTTS', 'SPPWNI', 'SPPWF', 'SPTJP',
    ];

    /**
     * Layanan Umum boleh diajukan oleh pemohon dari luar Kalurahan.
     * Daftar ini sengaja berbasis kode surat di backend, bukan nilai
     * service_flow dari client, agar layanan warga tidak ikut terbuka.
     *
     * @var list<string>
     */
    private const NON_RESIDENT_PUBLIC_CODES = [
        'SKU', 'SKP', 'SKK', 'SKJG',
    ];

    public function types(): JsonResponse
    {
        $types = LetterType::query()
            ->where('is_active', true)
            ->where('is_online', true)
            ->whereIn('code', self::ONLINE_CODES)
            ->with([
                'fields' => fn ($query) => $query->orderBy('sort_order'),
                'documents' => fn ($query) => $query->orderBy('letter_type_document_id'),
            ])
            ->orderBy('category')
            ->orderBy('letter_name')
            ->get()
            ->map(fn (LetterType $type) => $this->typePayload($type))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Data tipe surat online berhasil diambil.',
            'data' => $types,
        ]);
    }

    public function stats(): JsonResponse
    {
        $total = LetterRequest::query()->count();

        return response()->json([
            'success' => true,
            'message' => 'Statistik permohonan surat berhasil diambil.',
            'data' => [
                'total' => $total,
            ],
        ]);
    }

    public function checkNik(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
        ]);

        $citizen = Citizen::query()
            ->where('national_id', $validated['nik'])
            ->where('status', 'Active')
            ->first();

        if (! $citizen) {
            return response()->json([
                'success' => true,
                'data' => [
                    'registered' => false,
                    'masked_name' => null,
                    'resident' => null,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'registered' => true,
                'masked_name' => $this->maskedName($citizen->full_name),
                'resident' => $this->residentPayload($citizen),
            ],
        ]);
    }

    public function reusableDocuments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'letter_type_code' => ['required', 'string', Rule::in(self::ONLINE_CODES)],
        ]);

        $citizen = Citizen::query()
            ->where('national_id', $validated['nik'])
            ->where('status', 'Active')
            ->first();

        if (! $citizen) {
            return response()->json(['success' => true, 'data' => []]);
        }

        // Pengajuan yang dibuat sebelum fitur dokumen warga diperbaiki belum
        // memiliki citizen_document_id. Pulihkan lampiran lama yang file
        // fisiknya masih tersedia agar langsung bisa dipakai ulang.
        $this->backfillLegacyCitizenDocuments($citizen);

        $letterType = LetterType::query()
            ->where('code', $validated['letter_type_code'])
            ->with('documents')
            ->firstOrFail();

        $allowedTypes = $letterType->documents
            ->mapWithKeys(fn ($document) => [
                (int) $document->letter_type_document_id => $this->documentType($document->document_name),
            ]);

        $documents = CitizenDocument::query()
            ->where('citizen_id', $citizen->citizen_id)
            ->where('is_active', true)
            ->latest('last_used_at')
            ->latest('citizen_document_id')
            ->get()
            ->filter(fn (CitizenDocument $document) => $allowedTypes->contains($document->document_type))
            ->map(fn (CitizenDocument $document) => [
                'citizen_document_id' => $document->citizen_document_id,
                'document_type' => $document->document_type,
                'file_name' => $document->file_name,
                'file_size' => $document->file_size,
                'uploaded_at' => $document->created_at?->toISOString(),
            ])
            ->values();

        return response()->json(['success' => true, 'data' => $documents]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'letter_type_code' => ['required', 'string', Rule::in(self::ONLINE_CODES)],
            'applicant_name' => ['required', 'string', 'max:100'],
            'applicant_nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'applicant_phone' => ['nullable', 'string', 'max:15'],
            'applicant_address' => ['required', 'string', 'max:2000'],
            'form_data' => ['required'],
            'attachment_document_ids' => ['nullable', 'array'],
            'attachment_document_ids.*' => ['integer'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'reused_document_ids' => ['nullable', 'array'],
            'reused_document_ids.*' => ['integer'],
            'service_flow' => ['nullable', 'string', 'in:general,permit'],
            'is_new_resident' => ['nullable', 'boolean'],
        ]);

        $formData = $this->normalizeGenderFormData(
            $this->decodeFormData($validated['form_data']),
        );
        $letterType = LetterType::query()
            ->where('code', $validated['letter_type_code'])
            ->where('is_active', true)
            ->where('is_online', true)
            ->with(['fields', 'documents'])
            ->firstOrFail();

        $citizen = Citizen::query()
            ->where('national_id', $validated['applicant_nik'])
            ->where('status', 'Active')
            ->first();

        $isNewResident = (bool) ($validated['is_new_resident'] ?? false);
        $isAllowedNewResidentRegistration = $isNewResident
            && in_array($letterType->code, self::NEW_RESIDENT_CODES, true);
        $isAllowedNonResidentPublic = in_array($letterType->code, self::NON_RESIDENT_PUBLIC_CODES, true);

        if (! $citizen && ! $isAllowedNewResidentRegistration && ! $isAllowedNonResidentPublic) {
            throw ValidationException::withMessages([
                'applicant_nik' => 'NIK tidak terdaftar sebagai warga aktif.',
            ]);
        }

        $this->validatePublicFormData($letterType, $formData);

        $files = array_values($request->file('attachments', []));
        $documentIds = array_values($request->input('attachment_document_ids', []));
        $reusedDocumentIds = array_filter(
            (array) ($validated['reused_document_ids'] ?? []),
            fn ($value) => filled($value),
        );

        if (count($files) !== count($documentIds)) {
            throw ValidationException::withMessages([
                'attachments' => 'Setiap lampiran harus memiliki tipe dokumen yang sesuai.',
            ]);
        }

        $documents = $letterType->documents->keyBy('letter_type_document_id');
        foreach ($documentIds as $documentId) {
            if (! $documents->has((int) $documentId)) {
                throw ValidationException::withMessages([
                    'attachment_document_ids' => 'Tipe dokumen lampiran tidak sesuai dengan tipe surat.',
                ]);
            }
        }

        if ($reusedDocumentIds && ! $citizen) {
            throw ValidationException::withMessages([
                'reused_document_ids' => 'Dokumen tersimpan hanya dapat digunakan oleh warga yang sudah terdaftar.',
            ]);
        }

        $reusedDocuments = [];
        foreach ($reusedDocumentIds as $letterTypeDocumentId => $citizenDocumentId) {
            $requirement = $documents->get((int) $letterTypeDocumentId);
            if (! $requirement) {
                throw ValidationException::withMessages([
                    'reused_document_ids' => 'Jenis persyaratan dokumen tidak sesuai dengan surat.',
                ]);
            }

            $citizenDocument = CitizenDocument::query()
                ->where('citizen_document_id', (int) $citizenDocumentId)
                ->where('citizen_id', $citizen->citizen_id)
                ->where('is_active', true)
                ->first();

            if (! $citizenDocument
                || $citizenDocument->document_type !== $this->documentType($requirement->document_name)) {
                throw ValidationException::withMessages([
                    'reused_document_ids' => 'Dokumen tersimpan tidak cocok dengan persyaratan surat atau sudah tidak tersedia.',
                ]);
            }

            if (! Storage::disk('local')->exists($citizenDocument->storage_path)) {
                throw ValidationException::withMessages([
                    'reused_document_ids' => 'File dokumen tersimpan tidak ditemukan. Silakan unggah ulang dokumen tersebut.',
                ]);
            }

            $reusedDocuments[(int) $letterTypeDocumentId] = $citizenDocument;
        }

        $storedPaths = [];

        try {
            $letterRequest = DB::transaction(function () use (
                $validated,
                $formData,
                $letterType,
                $documents,
                $citizen,
                $files,
                $documentIds,
                $reusedDocuments,
                &$storedPaths,
            ): LetterRequest {
                $letterRequest = LetterRequest::create([
                    'citizen_id' => $citizen?->citizen_id,
                    'applicant_name' => $validated['applicant_name'],
                    'applicant_nik' => $validated['applicant_nik'],
                    'applicant_phone' => $validated['applicant_phone'] ?? null,
                    'applicant_address' => $validated['applicant_address'],
                    'letter_type_id' => $letterType->letter_type_id,
                    'signature_type' => $letterType->signature_method === 'digital' ? 'digital' : 'manual',
                    'source' => 'Online',
                    'form_data' => $formData,
                ]);

                foreach ($files as $index => $file) {
                    $storedPath = $file->store("letter-requests/{$letterRequest->letter_request_id}", 'public');
                    $storedPaths[] = $storedPath;
                    $requirement = $documents->get((int) $documentIds[$index]);

                    LetterRequestAttachment::create([
                        'letter_request_id' => $letterRequest->letter_request_id,
                        'letter_type_document_id' => (int) $documentIds[$index],
                        'citizen_document_id' => $this->storeCitizenDocument($citizen, $file, $requirement, $storedPaths),
                        'document_type' => $this->documentType($requirement?->document_name),
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => Storage::disk('public')->url($storedPath),
                        'uploaded_at' => now(),
                    ]);
                }

                foreach ($reusedDocuments as $documentId => $citizenDocument) {
                    $requestPath = "letter-requests/{$letterRequest->letter_request_id}/" . uniqid('reused-', true) . '-' . $citizenDocument->file_name;
                    Storage::disk('public')->put($requestPath, Storage::disk('local')->get($citizenDocument->storage_path));
                    $storedPaths[] = $requestPath;

                    LetterRequestAttachment::create([
                        'letter_request_id' => $letterRequest->letter_request_id,
                        'letter_type_document_id' => $documentId,
                        'citizen_document_id' => $citizenDocument->citizen_document_id,
                        'document_type' => $citizenDocument->document_type,
                        'file_name' => $citizenDocument->file_name,
                        'file_path' => Storage::disk('public')->url($requestPath),
                        'uploaded_at' => now(),
                    ]);

                    $citizenDocument->update(['last_used_at' => now()]);
                }

                return $letterRequest;
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $exception;
        }

        event(new NotificationAvailable(
            'letter',
            (int) $letterRequest->letter_request_id,
            'created',
            now()->toISOString(),
        ));

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dikirim.',
            'data' => [
                'request_code' => $letterRequest->request_code,
                'status' => $letterRequest->status,
                'submitted_at' => $letterRequest->submitted_at,
                'letter_type' => [
                    'code' => $letterType->code,
                    'name' => $letterType->letter_name,
                    'category' => $letterType->category,
                ],
            ],
        ], 201);
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'request_code' => ['required', 'string', 'max:30'],
        ]);

        $letterRequest = LetterRequest::query()
            ->with('letterType')
            ->where('request_code', $validated['request_code'])
            ->where('applicant_nik', $validated['nik'])
            ->whereHas('letterType', fn ($query) => $query->where('is_online', true)->whereIn('code', self::ONLINE_CODES))
            ->first();

        return response()->json([
            'success' => true,
            'data' => $letterRequest ? [$this->lookupPayload($letterRequest)] : [],
        ]);
    }

    public function pdf(
        Request $request,
        string $request_code,
        LetterPdfService $pdfService,
        DeathTemplateService $deathTemplateService,
    ) {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'download' => ['sometimes', 'boolean'],
        ]);

        $letterRequest = LetterRequest::query()
            ->with(['citizen', 'letterType.signer', 'verifier', 'authorizedSigner'])
            ->where('request_code', $request_code)
            ->where('applicant_nik', $validated['nik'])
            ->whereHas('letterType', fn ($query) => $query->where('is_online', true)->whereIn('code', self::ONLINE_CODES))
            ->firstOrFail();

        if (! in_array($letterRequest->status, ['authorized', 'completed'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF belum tersedia sebelum surat disetujui.',
            ], 422);
        }

        $code = strtoupper((string) ($letterRequest->letterType?->code ?? ''));
        $bladeView = (string) ($letterRequest->letterType?->blade_view ?? '');

        // Template kematian memakai normalizer khusus agar field camelCase dari
        // formulir public dapat dibaca oleh template Blade legacy.
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

    private function typePayload(LetterType $type): array
    {
        return [
            'letter_type_id' => $type->letter_type_id,
            'code' => $type->code,
            'letter_name' => $type->letter_name,
            'category' => $type->category,
            'processing_time' => $type->processing_time,
            'signature_method' => $type->signature_method,
            'fields' => $type->fields->map(fn ($field) => [
                'field_id' => $field->field_id,
                'field_label' => $field->field_label,
                'field_key' => $field->field_key,
                'field_type' => $field->field_type,
                'is_required' => $field->is_required,
                'options' => $field->options,
                'sort_order' => $field->sort_order,
            ])->values(),
            'documents' => $type->documents->map(fn ($document) => [
                'letter_type_document_id' => $document->letter_type_document_id,
                'document_name' => $document->document_name,
                'description' => $document->description,
                'is_required' => $document->is_required,
                'document_type' => $this->documentType($document->document_name),
            ])->values(),
        ];
    }

    private function documentType(?string $name): string
    {
        $name = strtolower(trim((string) $name));
        $name = trim(preg_replace('/[^a-z0-9]+/', ' ', $name) ?: '');

        $base = match (true) {
            str_contains($name, 'kartu keluarga') || preg_match('/\bkk\b/', $name) === 1 => 'kk',
            str_contains($name, 'paspor') || str_contains($name, 'passport') => 'paspor',
            str_contains($name, 'akta kelahiran') || str_contains($name, 'akta lahir') || str_contains($name, 'surat keterangan lahir') => 'akta_kelahiran',
            str_contains($name, 'akta perkawinan') || str_contains($name, 'buku nikah') || str_contains($name, 'akta nikah') => 'akta_perkawinan',
            str_contains($name, 'akta perceraian') || str_contains($name, 'surat cerai') || str_contains($name, 'cerai') => 'akta_perceraian',
            str_contains($name, 'surat kematian') || str_contains($name, 'akta kematian') || str_contains($name, 'meninggal') || str_contains($name, 'almarhum') => 'surat_kematian',
            str_contains($name, 'pas foto') || str_contains($name, 'pass photo') || preg_match('/\bfoto\b/', $name) === 1 => 'pas_foto',
            str_contains($name, 'pbb') => 'pbb',
            str_contains($name, 'shm') || str_contains($name, 'letter c') => 'letter_c',
            str_contains($name, 'rt rw') || str_contains($name, 'rt atau rw') => 'surat_rt_rw',
            str_contains($name, 'ktp') || str_contains($name, 'kartu tanda penduduk') => 'ktp',
            default => 'lainnya',
        };

        if (! in_array($base, ['ktp', 'kk', 'akta_kelahiran', 'akta_perkawinan', 'akta_perceraian', 'surat_kematian', 'paspor'], true)) {
            return $base === 'lainnya'
                ? 'lainnya:' . mb_substr($name !== '' ? $name : 'dokumen', 0, 71)
                : $base;
        }

        $scope = match (true) {
            str_contains($name, 'pasangan') || str_contains($name, 'suami') || str_contains($name, 'istri') || str_contains($name, 'calon') => 'spouse',
            str_contains($name, 'ayah') || str_contains($name, 'ibu') || str_contains($name, 'orang tua') || str_contains($name, 'wali') => 'parent',
            str_contains($name, 'ahli waris') => 'heir',
            str_contains($name, 'saksi') => 'witness',
            str_contains($name, 'penerima kuasa') || str_contains($name, 'pemberi kuasa') => 'attorney',
            str_contains($name, 'kematian') || str_contains($name, 'meninggal') || str_contains($name, 'almarhum') || str_contains($name, 'jenazah') => 'deceased',
            default => 'applicant',
        };

        return "{$base}:{$scope}";
    }

    private function storeCitizenDocument(?Citizen $citizen, $file, $requirement, array &$storedPaths): ?int
    {
        if (! $citizen || ! $requirement) {
            return null;
        }

        $hash = hash_file('sha256', $file->getRealPath());
        $type = $this->documentType($requirement->document_name);
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $privatePath = "citizen-documents/{$citizen->citizen_id}/{$hash}.{$extension}";

        if (! Storage::disk('local')->exists($privatePath)) {
            Storage::disk('local')->putFileAs(
                "citizen-documents/{$citizen->citizen_id}",
                $file,
                "{$hash}.{$extension}",
            );
        }

        $document = CitizenDocument::query()->firstOrCreate(
            [
                'citizen_id' => $citizen->citizen_id,
                'document_type' => $type,
                'file_hash' => $hash,
            ],
            [
                'file_name' => $file->getClientOriginalName(),
                'storage_path' => $privatePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_active' => true,
            ],
        );

        $document->update([
            'file_name' => $file->getClientOriginalName(),
            'is_active' => true,
            'last_used_at' => now(),
        ]);

        return $document->citizen_document_id;
    }

    private function backfillLegacyCitizenDocuments(Citizen $citizen): void
    {
        $attachments = LetterRequestAttachment::query()
            ->whereHas('letterRequest', fn ($query) => $query->where('citizen_id', $citizen->citizen_id))
            ->where(function ($query): void {
                $query->whereNull('citizen_document_id')
                    ->orWhereHas('citizenDocument', fn ($documentQuery) => $documentQuery->where('document_type', 'lainnya'))
                    ->orWhere(function ($legacyKtpQuery): void {
                        $legacyKtpQuery
                            ->where('document_type', 'kk:applicant')
                            ->whereHas('letterRequest.letterType', fn ($typeQuery) => $typeQuery->where('code', 'SPKTP'));
                    });
            })
            ->with(['letterTypeDocument', 'citizenDocument', 'letterRequest.letterType.documents'])
            ->get();

        // Versi lama public mengirimkan "Pas foto" dengan ID persyaratan KK.
        // Bila satu pengajuan KTP memiliki dua lampiran pada ID KK dengan file
        // berbeda, lampiran kedua adalah pas foto sesuai urutan form publik.
        $legacyPhotoRequirements = [];
        foreach ($attachments->groupBy('letter_request_id') as $requestAttachments) {
            $letterType = $requestAttachments->first()?->letterRequest?->letterType;
            if ($letterType?->code !== 'SPKTP') continue;

            $photoRequirement = $letterType->documents->first(
                fn ($document): bool => preg_match('/pas\s*foto|pass\s*photo/i', (string) $document->document_name) === 1,
            );
            if (! $photoRequirement) continue;

            foreach ($requestAttachments->groupBy('letter_type_document_id') as $sameRequirement) {
                if ($sameRequirement->count() < 2) continue;
                $seenHashes = [];
                foreach ($sameRequirement as $attachment) {
                    $publicPath = (string) $attachment->file_path;
                    $publicPath = (string) (parse_url($publicPath, PHP_URL_PATH) ?: $publicPath);
                    $publicPath = preg_replace('#^/storage/#', '', $publicPath) ?: $publicPath;
                    if (! Storage::disk('public')->exists($publicPath)) continue;

                    $hash = hash('sha256', Storage::disk('public')->get($publicPath));
                    if ($seenHashes !== [] && ! in_array($hash, $seenHashes, true)) {
                        $legacyPhotoRequirements[$attachment->attachment_id] = $photoRequirement;
                    }
                    $seenHashes[] = $hash;
                }
            }
        }

        foreach ($attachments as $attachment) {
            $publicPath = (string) $attachment->file_path;
            $publicPath = (string) (parse_url($publicPath, PHP_URL_PATH) ?: $publicPath);
            $publicPath = preg_replace('#^/storage/#', '', $publicPath) ?: $publicPath;

            if (! Storage::disk('public')->exists($publicPath)) {
                continue;
            }

            $contents = Storage::disk('public')->get($publicPath);
            $hash = hash('sha256', $contents);
            $requirement = $legacyPhotoRequirements[$attachment->attachment_id] ?? $attachment->letterTypeDocument;
            $requirementName = $requirement?->document_name ?: $attachment->document_type;
            $type = $this->documentType($requirementName);
            $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION) ?: 'bin');
            $privatePath = "citizen-documents/{$citizen->citizen_id}/{$hash}.{$extension}";

            if (! Storage::disk('local')->exists($privatePath)) {
                Storage::disk('local')->put($privatePath, $contents);
            }

            $document = CitizenDocument::query()->firstOrCreate(
                [
                    'citizen_id' => $citizen->citizen_id,
                    'document_type' => $type,
                    'file_hash' => $hash,
                ],
                [
                    'file_name' => $attachment->file_name,
                    'storage_path' => $privatePath,
                    'mime_type' => Storage::disk('public')->mimeType($publicPath),
                    'file_size' => Storage::disk('public')->size($publicPath),
                    'is_active' => true,
                    'last_used_at' => now(),
                ],
            );

            $attachment->update([
                'letter_type_document_id' => $requirement?->letter_type_document_id ?? $attachment->letter_type_document_id,
                'citizen_document_id' => $document->citizen_document_id,
                'document_type' => $type,
            ]);
        }
    }

    /**
     * Sinkronisasi satu kali untuk lampiran lama yang dibuat sebelum citizen_documents.
     * Dipakai endpoint admin agar dokumen historis juga muncul pada Data Warga.
     */
    public function syncLegacyDocuments(Citizen $citizen): void
    {
        $this->backfillLegacyCitizenDocuments($citizen);
    }

    private function residentPayload(Citizen $citizen): array
    {
        $headOfFamilyName = null;
        $spouse = null;
        if (filled($citizen->family_card_number)) {
            $familyMembers = Citizen::query()
                ->where('family_card_number', $citizen->family_card_number)
                ->where('status', 'Active')
                ->get();

            $headOfFamilyName = $familyMembers
                ->first(function (Citizen $member): bool {
                    return preg_match('/kepala|kk/i', (string) $member->family_relationship) === 1;
                })
                ?->full_name;

            $spouseRelations = $this->spouseRelationsFor($citizen);
            if ($spouseRelations !== null && $this->isMarriedStatus($citizen->marital_status)) {
                $spouse = $familyMembers
                    ->where('citizen_id', '!=', $citizen->citizen_id)
                    ->filter(fn (Citizen $member): bool => $this->isMarriedStatus($member->marital_status))
                    ->first(function (Citizen $member) use ($spouseRelations, $citizen): bool {
                        $relation = strtolower((string) $member->family_relationship);
                        $relationMatches = collect($spouseRelations)->contains(
                            fn (string $allowed): bool => str_contains($relation, $allowed),
                        );
                        if (!$relationMatches) return false;

                        $gender = strtolower((string) $citizen->gender);
                        $memberGender = strtolower((string) $member->gender);
                        return ($gender === 'laki-laki' && $memberGender === 'perempuan')
                            || ($gender === 'perempuan' && $memberGender === 'laki-laki');
                    });
            }

            if ($headOfFamilyName === null) {
                $headOfFamilyName = $familyMembers
                    ->first(function (Citizen $member): bool {
                        return preg_match('/kepala|kk/i', (string) $member->family_relationship) === 1;
                    })
                    ?->full_name;
            }
        }

        if ($headOfFamilyName === null && preg_match('/kepala|kk/i', (string) $citizen->family_relationship)) {
            $headOfFamilyName = $citizen->full_name;
        }

        return [
            'nik' => $citizen->national_id,
            'titleCode' => $citizen->title_code,
            'familyCardNumber' => $citizen->family_card_number,
            'headOfFamilyName' => $headOfFamilyName,
            'fullName' => $citizen->full_name,
            'gender' => $citizen->gender,
            'birthPlace' => $citizen->birth_place,
            'birthDate' => $citizen->birth_date?->format('Y-m-d'),
            'age' => $citizen->age,
            'address' => $citizen->address,
            'ktpAddress' => $citizen->ktp_address ?: $citizen->address,
            'dusun' => $citizen->dusun,
            'rt' => $citizen->rt,
            'rw' => $citizen->rw,
            'rtHeadName' => $citizen->rt_head_name,
            'rwHeadName' => $citizen->rw_head_name,
            'phoneNumber' => $citizen->phone_number,
            'birthCertificateStatus' => $citizen->birth_certificate_status,
            'birthCertificateNumber' => $citizen->birth_certificate_number,
            'bloodType' => $citizen->blood_type,
            'occupation' => $citizen->occupation,
            'education' => $citizen->education,
            'maritalStatus' => $citizen->marital_status,
            'marriageCertificateStatus' => $citizen->marriage_certificate_status,
            'marriageCertificateNumber' => $citizen->marriage_certificate_number,
            'marriageDate' => $citizen->marriage_date?->format('Y-m-d'),
            'divorceCertificateStatus' => $citizen->divorce_certificate_status,
            'divorceCertificateNumber' => $citizen->divorce_certificate_number,
            'divorceDate' => $citizen->divorce_date?->format('Y-m-d'),
            'familyRelationship' => $citizen->family_relationship,
            'physicalDisability' => $citizen->physical_disability,
            'disabilityStatus' => $citizen->disability_status,
            'religion' => $citizen->religion,
            'beliefOrganization' => $citizen->belief_organization,
            'motherNationalId' => $citizen->mother_national_id,
            'motherName' => $citizen->mother_name,
            'fatherNationalId' => $citizen->father_national_id,
            'fatherName' => $citizen->father_name,
            'nationality' => $citizen->nationality ?: 'WNI',
            'previousAddress' => $citizen->previous_address,
            'passportNumber' => $citizen->passport_number,
            'passportExpiryDate' => $citizen->passport_expiry_date?->format('Y-m-d'),
            'spouse' => $spouse ? [
                'nik' => $spouse->national_id,
                'fullName' => $spouse->full_name,
                'gender' => $spouse->gender,
                'birthPlace' => $spouse->birth_place,
                'birthDate' => $spouse->birth_date?->format('Y-m-d'),
                'birthPlaceDate' => $this->formatBirthPlaceDate($spouse),
                'religion' => $spouse->religion,
                'occupation' => $spouse->occupation,
                'address' => $spouse->address,
                'marriageCertificateNumber' => $spouse->marriage_certificate_number,
                'familyRelationship' => $spouse->family_relationship,
            ] : null,
        ];
    }

    private function isMarriedStatus(?string $status): bool
    {
        $value = strtolower(trim((string) $status));
        if ($value === '' || preg_match('/belum|cerai|janda|duda|jejaka|perawan/i', $value)) return false;

        return preg_match('/kawin|menikah/i', $value) === 1;
    }

    private function spouseRelationsFor(Citizen $citizen): ?array
    {
        $relation = strtolower(trim((string) $citizen->family_relationship));
        if ($relation === '' || preg_match('/anak|menantu|cucu|orang ?tua|saudara|famili/i', $relation)) return null;
        if (str_contains($relation, 'istri')) return ['suami', 'kepala'];
        if (str_contains($relation, 'suami')) return ['istri', 'kepala'];
        if (preg_match('/kepala|\bkk\b/i', $relation)) {
            return strtolower((string) $citizen->gender) === 'laki-laki' ? ['istri'] : ['suami'];
        }

        return null;
    }

    private function formatBirthPlaceDate(Citizen $citizen): ?string
    {
        $place = trim((string) $citizen->birth_place);
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $date = $citizen->birth_date
            ? ltrim($citizen->birth_date->format('d'), '0') . ' '
                . ($monthNames[(int) $citizen->birth_date->format('n')] ?? $citizen->birth_date->format('F'))
                . ' ' . $citizen->birth_date->format('Y')
            : null;
        if ($place === '' && $date === null) return null;
        if ($place === '') return $date;
        if ($date === null) return $place;
        return $place . ', ' . $date;
    }

    private function maskedName(?string $name): ?string
    {
        $name = trim((string) $name);
        if ($name === '') return null;

        return mb_substr($name, 0, 1) . str_repeat('*', max(2, mb_strlen($name) - 1));
    }

    private function decodeFormData(mixed $value): array
    {
        if (is_array($value)) return $value;
        if (! is_string($value) || mb_strlen($value) > 200000) {
            throw ValidationException::withMessages(['form_data' => 'Data isian surat tidak valid.']);
        }

        try {
            $decoded = json_decode($value, true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::withMessages(['form_data' => 'Data isian surat harus berupa JSON yang valid.']);
        }

        if (! is_array($decoded)) {
            throw ValidationException::withMessages(['form_data' => 'Data isian surat harus berupa objek.']);
        }

        return $decoded;
    }

    /**
     * Dropdown publik menampilkan jenis kelamin dengan nomor ("1. Laki-laki"
     * / "2. Perempuan"), sedangkan metadata backend menyimpan nilai tanpa
     * nomor. Normalisasi dilakukan rekursif agar field pasangan/anak/baris
     * berulang juga tetap kompatibel.
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

    private function validatePublicFormData(LetterType $letterType, array $formData): void
    {
        if (count($formData) > 150) {
            throw ValidationException::withMessages(['form_data' => 'Jumlah field surat terlalu banyak.']);
        }

        // The public Vue forms intentionally use human-friendly field keys,
        // while legacy admin field seeders use template-specific keys. Keep
        // the stored payload compatible with LetterPdfService and validate
        // select values whenever a public key has a corresponding metadata
        // field. Required fields are also checked client-side before submit.
        $fields = $letterType->fields->keyBy('field_key');
        foreach ($formData as $key => $value) {
            if (! is_string($key) || mb_strlen($key) > 100) {
                throw ValidationException::withMessages(['form_data' => 'Kunci field surat tidak valid.']);
            }

            if ($fields->has($key) && $fields[$key]->field_type === 'select' && $value !== null && $value !== '') {
                $options = array_map('strval', $fields[$key]->options ?? []);
                if ($options !== [] && ! in_array((string) $value, $options, true)) {
                    throw ValidationException::withMessages([
                        "form_data.{$key}" => "Nilai field {$fields[$key]->field_label} tidak valid.",
                    ]);
                }
            }
        }
    }

    private function lookupPayload(LetterRequest $letterRequest): array
    {
        return [
            'request_code' => $letterRequest->request_code,
            'status' => $letterRequest->status,
            'letter_number' => LetterNumberFormatter::forDisplay(
                $letterRequest->letter_number,
                $letterRequest->letterType?->classification_index,
            ),
            'submitted_at' => $letterRequest->submitted_at,
            'verified_at' => $letterRequest->verified_at,
            'authorized_at' => $letterRequest->authorized_at,
            'completed_at' => $letterRequest->completed_at,
            'rejection_reason' => $letterRequest->status === 'rejected'
                ? $letterRequest->notes
                : null,
            'can_download' => in_array($letterRequest->status, ['authorized', 'completed'], true),
            'letter_type' => [
                'code' => $letterRequest->letterType?->code,
                'name' => $letterRequest->letterType?->letter_name,
                'category' => $letterRequest->letterType?->category,
                'processing_time' => $letterRequest->letterType?->processing_time,
            ],
        ];
    }
}
