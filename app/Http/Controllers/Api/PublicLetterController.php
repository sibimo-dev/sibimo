<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\NotificationAvailable;
use App\Models\Citizen;
use App\Models\LetterRequest;
use App\Models\LetterRequestAttachment;
use App\Models\LetterType;
use App\Services\DeathTemplateService;
use App\Services\LetterPdfService;
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
            'service_flow' => ['nullable', 'string', 'in:general,permit'],
            'is_new_resident' => ['nullable', 'boolean'],
        ]);

        $formData = $this->decodeFormData($validated['form_data']);
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

        if (! $citizen && ! $isAllowedNewResidentRegistration) {
            throw ValidationException::withMessages([
                'applicant_nik' => 'NIK tidak terdaftar sebagai warga aktif.',
            ]);
        }

        $this->validatePublicFormData($letterType, $formData);

        $files = array_values($request->file('attachments', []));
        $documentIds = array_values($request->input('attachment_document_ids', []));

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

        $storedPaths = [];

        try {
            $letterRequest = DB::transaction(function () use (
                $validated,
                $formData,
                $letterType,
                $citizen,
                $files,
                $documentIds,
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

                    LetterRequestAttachment::create([
                        'letter_request_id' => $letterRequest->letter_request_id,
                        'letter_type_document_id' => (int) $documentIds[$index],
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => Storage::disk('public')->url($storedPath),
                        'uploaded_at' => now(),
                    ]);
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
            ])->values(),
        ];
    }

    private function residentPayload(Citizen $citizen): array
    {
        return [
            'nik' => $citizen->national_id,
            'familyCardNumber' => $citizen->family_card_number,
            'fullName' => $citizen->full_name,
            'gender' => $citizen->gender,
            'birthPlace' => $citizen->birth_place,
            'birthDate' => $citizen->birth_date?->format('Y-m-d'),
            'address' => $citizen->address,
            'ktpAddress' => $citizen->ktp_address ?: $citizen->address,
            'phoneNumber' => $citizen->phone_number,
            'occupation' => $citizen->occupation,
            'education' => $citizen->education,
            'maritalStatus' => $citizen->marital_status,
            'religion' => $citizen->religion,
        ];
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
            'letter_number' => $letterRequest->letter_number,
            'submitted_at' => $letterRequest->submitted_at,
            'verified_at' => $letterRequest->verified_at,
            'authorized_at' => $letterRequest->authorized_at,
            'completed_at' => $letterRequest->completed_at,
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
