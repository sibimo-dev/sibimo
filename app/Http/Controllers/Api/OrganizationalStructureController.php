<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\OrganizationalStructureSetting;
use App\Models\Region;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\StaffAssignment;
use App\Models\StaffPerson;

class OrganizationalStructureController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data struktur organisasi berhasil diambil.',
            'data' => [$this->structurePayload()],
        ]);
    }

    public function show(int $organizational_structure_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail struktur organisasi berhasil diambil.',
            'data' => $this->structurePayload(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->validated($request);
        $this->replaceStaff($request, $payload['levels']);
        $this->saveSettings($payload);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil dibuat.',
            'data' => $this->structurePayload($payload['title'], $payload['status']),
        ], 201);
    }

    public function update(Request $request, int $organizational_structure_id): JsonResponse
    {
        $payload = $this->validated($request);
        $this->replaceStaff($request, $payload['levels']);
        $this->saveSettings($payload);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil diperbarui.',
            'data' => $this->structurePayload($payload['title'], $payload['status']),
        ]);
    }

    public function destroy(int $organizational_structure_id): JsonResponse
    {
        StaffAssignment::query()->active()->managed()
            ->update(['end_date' => now()->subDay()->toDateString()]);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil dihapus.',
        ]);
    }

    private function validated(Request $request): array
    {
        $this->decodeJsonField($request, 'levels');
        $this->decodeJsonField($request, 'former_officials');
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'levels' => ['required', 'array'],
            'status' => ['nullable', Rule::in(['Draft', 'Published'])],
            'published_at' => ['nullable', 'date'],
            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'signatures.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'former_officials' => ['nullable', 'array'],
            'former_officials.*.id' => ['nullable', 'string', 'max:64'],
            'former_officials.*.jabatan' => ['required', 'string', 'max:150'],
            'former_officials.*.nama' => ['required', 'string', 'max:150'],
            'former_officials.*.rt_number' => ['nullable', 'string', 'max:20'],
            'former_officials.*.rw_number' => ['nullable', 'string', 'max:20'],
            'former_officials.*.padukuhan' => ['nullable', 'string', 'max:100'],
            'former_officials.*.start_year' => ['required', 'integer', 'between:1900,2100'],
            'former_officials.*.end_year' => ['required', 'integer', 'between:1900,2100', 'gte:former_officials.*.start_year'],
            'former_officials.*.desc' => ['nullable', 'string', 'max:500'],
            'show_former_officials' => ['nullable', 'boolean'],
            'sk_documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'former_officials.*.staff_id' => ['nullable', 'integer'],
            'former_officials.*.gender' => ['nullable', 'string', 'max:20'],
            'former_officials.*.birth_date' => ['nullable', 'date'],
            'former_officials.*.education' => ['nullable', 'string', 'max:50'],
            'former_officials.*.major' => ['nullable', 'string', 'max:100'],
            'former_officials.*.school_name' => ['nullable', 'string', 'max:150'],
            'former_officials.*.graduation_year' => ['nullable', 'integer', 'between:1950,2100'],
            'former_officials.*.sk_number' => ['nullable', 'string', 'max:100'],
        ]);
        Validator::make($validated['levels'], [
            '*.level' => ['required', 'string'],
            '*.pimpinan' => ['nullable', 'boolean'],
            '*.slider' => ['nullable', 'boolean'],
            '*.centerOnDesktop' => ['nullable', 'boolean'],
            '*.people' => ['required', 'array'],
            '*.people.*.staff_id' => ['nullable', 'integer'],
            '*.people.*.name' => ['required', 'string'],
            '*.people.*.title' => ['required', 'string'],
            '*.people.*.desc' => ['nullable', 'string'],
            '*.people.*.photo' => ['nullable', 'string'],
            '*.people.*.signature' => ['nullable', 'string'],
            '*.people.*.start_year' => ['nullable', 'integer', 'between:1900,2100'],
            '*.people.*.end_year' => ['nullable', 'integer', 'between:1900,2100'],
            '*.people.*.gender' => ['nullable', 'string', 'max:20'],
            '*.people.*.age' => ['nullable', 'integer', 'between:0,120'],
            '*.people.*.education' => ['nullable', 'string', 'max:50'],
            '*.people.*.major' => ['nullable', 'string', 'max:150'],
            '*.people.*.school_name' => ['nullable', 'string', 'max:150'],
            '*.people.*.graduation_year' => ['nullable', 'integer', 'between:1900,2100'],
            '*.people.*.term_end_date' => ['nullable', 'date'],
            '*.people.*.rt_number' => ['nullable', 'string', 'max:20'],
            '*.people.*.rw_number' => ['nullable', 'string', 'max:20'],
            '*.people.*.padukuhan' => ['nullable', 'string', 'max:150'],
        ])->validate();

        return $validated;
    }

    /**
     * Sinkronkan tabel staff (non-penandatangan) dengan payload tanpa menghapus semuanya.
     * Baris yang sudah ada diperbarui di tempat supaya staff_id tetap sama dan tautan
     * regions.head_staff_id tidak menjadi NULL. Hanya baris yang benar-benar dihapus
     * admin yang di-delete.
     */
    private const LEVEL_ORDER = [
        'Lurah',
        'Carik',
        'Kepala Urusan (Sekretariat & Keuangan)',
        'Kepala Seksi',
        'Staff Pamong Kalurahan',
        'Dukuh (Kepala Padukuhan)',
        'Ketua RT & Ketua RW',
    ];

    // Urutan baku daftar pamong. Jangan bergantung pada ID assignment karena
    // assignment baru/hasil edit dapat membuat urutan daftar berubah sendiri.
    private const POSITION_ORDER = [
        'Lurah',
        'Carik',
        'Kaur Danarta',
        'Kaur Tata Laksana',
        'Kaur Pangripta',
        'Kamituwa',
        'Ulu-Ulu',
        'Jagabaya',
        'Staff Pamong Kalurahan',
    ];
    
    private ?Collection $regionCache = null;
    
    // Pengelompokan bagan diturunkan dari jabatan, bukan kolom database.
    private function levelOf(string $position): string
    {
        $position = trim((string) preg_replace('/\s+Kalurahan\s+Bimomartani\s*$/i', '', $position));

        return match (true) {
            $position === 'Lurah' => 'Lurah',
            $position === 'Carik' => 'Carik',
            str_starts_with($position, 'Kaur') => 'Kepala Urusan (Sekretariat & Keuangan)',
            in_array($position, ['Kamituwa', 'Ulu-Ulu', 'Jagabaya'], true) => 'Kepala Seksi',
            in_array($position, ['Ketua RT', 'Ketua RW'], true) => 'Ketua RT & Ketua RW',
            str_starts_with($position, 'Dukuh') => 'Dukuh (Kepala Padukuhan)',
            default => 'Staff Pamong Kalurahan',
        };
    }

    private function positionSortKey(string $position, int $id = 0): array
    {
        $position = trim($position);
        $dukuhRank = 0;
        if (preg_match('/^Dukuh\s+([IVXLCDM]+|\d+)/i', $position, $match)) {
            $value = strtoupper($match[1]);
            $dukuhRank = ctype_digit($value) ? (int) $value : $this->romanToInt($value);
            return [8, $dukuhRank, $id];
        }

        $rank = array_search($position, self::POSITION_ORDER, true);

        return [$rank === false ? 999 : $rank, 0, $id];
    }

    private function romanToInt(string $value): int
    {
        $map = ['I' => 1, 'V' => 5, 'X' => 10, 'L' => 50, 'C' => 100, 'D' => 500, 'M' => 1000];
        $total = 0;
        $previous = 0;
        foreach (array_reverse(str_split($value)) as $letter) {
            $number = $map[$letter] ?? 0;
            $total += $number < $previous ? -$number : $number;
            $previous = max($previous, $number);
        }

        return $total;
    }
    
    private function nullIfEmpty(mixed $value): mixed
    {
        return ($value === '' || $value === null) ? null : $value;
    }
    
    // "Dukuh I Krebet" -> region_id padukuhan "KREBET".
    private function matchRegionId(string $position): ?int
    {
        if (!preg_match('/^Dukuh\s+(?:[IVXLCDM]+|\d+)\s+(.+)$/i', $position, $m)) return null;
        $this->regionCache ??= Region::query()->get(['region_id', 'name']);
        $name = mb_strtolower(trim($m[1]));
        $hit = $this->regionCache->first(fn ($r) => mb_strtolower($r->name) === $name)
            ?? $this->regionCache->first(fn ($r) => str_contains(mb_strtolower($r->name), $name)
                || str_contains($name, mb_strtolower($r->name)));
        return $hit?->region_id;
    }

    private function areaAssignment(string $title, array $person, ?StaffAssignment $current = null): array
    {
        $isRt = $title === 'Ketua RT';
        $isRw = $title === 'Ketua RW';
        $padukuhanId = str_starts_with($title, 'Dukuh')
            ? $this->matchRegionId($title)
            : null;

        if (!$isRt && !$isRw) {
            return [
                'padukuhan_id' => $padukuhanId ?? $current?->padukuhan_id,
                'rw_id' => null,
                'rt_id' => null,
            ];
        }

        $padukuhanValue = $person['padukuhan'] ?? '';
        if (is_array($padukuhanValue)) {
            $padukuhanValue = $padukuhanValue['value'] ?? $padukuhanValue['label'] ?? '';
        }
        $padukuhan = trim((string) $padukuhanValue);
        $rwNumber = trim((string) ($person['rw_number'] ?? ''));
        $rtNumber = trim((string) ($person['rt_number'] ?? ''));
        $region = Region::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($padukuhan)])
            ->first();

        if (!$region && $current?->padukuhan_id) {
            $region = Region::find($current->padukuhan_id);
        }

        if ($current && $rwNumber === '' && $current->rw_id) {
            $rwNumber = (string) Rw::find($current->rw_id)?->number;
        }
        if ($current && $rtNumber === '' && $current->rt_id) {
            $rtNumber = (string) Rt::find($current->rt_id)?->number;
        }

        if (!$region || $rwNumber === '' || ($isRt && $rtNumber === '')) {
            throw ValidationException::withMessages([
                'levels' => "Wilayah {$title} belum lengkap. Isi padukuhan, nomor RW" . ($isRt ? ', dan nomor RT.' : '.'),
            ]);
        }

        $rw = Rw::query()->where('region_id', $region->region_id)->get()
            ->first(fn (Rw $item) => $this->sameAreaNumber($item->number, $rwNumber));
        $rw ??= Rw::query()->create([
            'region_id' => $region->region_id,
            'number' => $this->normalizeAreaNumber($rwNumber),
        ]);

        $rt = null;
        if ($isRt) {
            $rt = Rt::query()->where('rw_id', $rw->id)->get()
                ->first(fn (Rt $item) => $this->sameAreaNumber($item->number, $rtNumber));
            $rt ??= Rt::query()->create([
                'rw_id' => $rw->id,
                'number' => $this->normalizeAreaNumber($rtNumber),
            ]);
        }

        return [
            'padukuhan_id' => $region->region_id,
            'rw_id' => $rw->id,
            'rt_id' => $rt?->id,
        ];
    }

    private function sameAreaNumber(mixed $left, mixed $right): bool
    {
        $normalize = static fn ($value) => preg_replace('/^0+/', '', trim((string) $value)) ?: '0';

        return $normalize($left) === $normalize($right);
    }

    private function normalizeAreaNumber(string $value): string
    {
        return ctype_digit($value) ? str_pad((string) ((int) $value), 2, '0', STR_PAD_LEFT) : $value;
    }
    
    private function replaceSkToken(?string $value, array $files): ?string
    {
        if (!$value || !str_starts_with($value, 'upload-sk:')) return $value;
        $token = substr($value, 10);
        $file = $files[$token] ?? null;
        if (!$file) {
            throw ValidationException::withMessages(['sk_documents' => "File SK untuk token {$token} tidak ditemukan."]);
        }
        return Storage::disk('public')->url($file->store('profile/sk', 'public'));
    }
    
    // Cari penugasan aktif yang cocok: lewat staff_id dari FE (utamakan jabatan yang sama),
    // atau untuk klien lama lewat nama + jabatan.
    private function findAssignment(Collection $existing, array &$claimed, array $person): ?StaffAssignment
    {
        $pool = $existing->reject(fn ($a) => in_array($a->id, $claimed, true));
        $staffId = isset($person['staff_id']) ? (int) $person['staff_id'] : null;
    
        if ($staffId) {
            $mine = $pool->where('staff_id', $staffId);
            return $mine->firstWhere('position', $person['title']) ?? $mine->first();
        }
    
        return $pool->first(fn ($a) => $a->person?->name === $person['name'] && $a->position === $person['title']);
    }
    
    private function replaceStaff(Request $request, array $levels): void
    {
        DB::transaction(function () use ($request, $levels): void {
            $photoFiles = $request->allFiles()['photos'] ?? [];
            $signatureFiles = $request->allFiles()['signatures'] ?? [];
            $skFiles = $request->allFiles()['sk_documents'] ?? [];
    
            // RT/RW juga dikelola dari Struktur Organisasi, sehingga harus
            // ikut dimuat agar assignment lama diperbarui, bukan dibuat ulang.
            $existing = StaffAssignment::query()->active()->with('person')->get();
            $claimed = [];
            $seenDukuh = [];
            $obsoleteMedia = [];
            $signatures = [];
    
            foreach ($levels as $level) {
                foreach ($level['people'] as $p) {
                    $title = $p['title'];
                    $isDukuh = str_starts_with($title, 'Dukuh');
    
                    // Satu wilayah + jabatan hanya satu penugasan aktif.
                    if ($isDukuh) {
                        if (isset($seenDukuh[$title])) {
                            throw ValidationException::withMessages(['levels' => "Jabatan {$title} hanya boleh punya satu penugasan aktif."]);
                        }
                        $seenDukuh[$title] = true;
                    }
    
                    $photo = $this->replacePhotoToken($p['photo'] ?? null, $photoFiles);
                    $signature = $this->replaceSignatureToken($p['signature'] ?? null, $signatureFiles);
    
                    $assignment = $this->findAssignment($existing, $claimed, $p);
                    $area = $this->areaAssignment($title, $p, $assignment);
                    $person = $assignment?->person
                        ?? (!empty($p['staff_id']) ? StaffPerson::find((int) $p['staff_id']) : null)
                        ?? new StaffPerson();
    
                    foreach ([[$person->photo, $photo], [$person->signature_image, $signature]] as [$old, $new]) {
                        if ($old && $old !== $new) $obsoleteMedia[] = $old;
                    }
    
                    // Data orang.
                    $person->fill([
                        'name' => $p['name'],
                        'photo' => $photo,
                        'signature_image' => $signature,
                        'gender' => $this->nullIfEmpty($p['gender'] ?? null),
                        'birth_date' => $this->nullIfEmpty($p['birth_date'] ?? null),
                        'education' => $this->nullIfEmpty($p['education'] ?? null),
                        'major' => $this->nullIfEmpty($p['major'] ?? null),
                        'school_name' => $this->nullIfEmpty($p['school_name'] ?? null),
                        'graduation_year' => $this->nullIfEmpty($p['graduation_year'] ?? null),
                    ])->save();
    
                    // Tanggal: data lama yang hanya punya tahun dikonversi ke 1 Jan / 31 Des (hanya untuk penugasan baru).
                    $start = $this->nullIfEmpty($p['start_date'] ?? null);
                    $end = $this->nullIfEmpty($p['end_date'] ?? null);
                    if (!$assignment) {
                        $start ??= !empty($p['start_year']) ? "{$p['start_year']}-01-01" : null;
                        $end ??= !empty($p['end_year']) ? "{$p['end_year']}-12-31" : null;
                    }
    
                    $history = collect($p['position_history'] ?? [])->map(fn ($h) => [
                        'sk' => $h['sk'] ?? null,
                        'date' => $this->nullIfEmpty($h['date'] ?? null),
                        'official' => $h['official'] ?? null,
                        'document' => $this->replaceSkToken($h['document'] ?? null, $skFiles),
                        'document_name' => $h['document_name'] ?? null,
                    ])->values()->all();
    
                    $attributes = [
                        'staff_id' => $person->id,
                        'position' => $title,
                        'padukuhan_id' => $area['padukuhan_id'],
                        'rw_id' => $area['rw_id'],
                        'rt_id' => $area['rt_id'],
                        'description' => $p['desc'] ?? null,
                        'start_date' => $start,
                        'end_date' => $end,
                        'term_end_date' => $this->nullIfEmpty($p['term_end_date'] ?? null),
                        'sk_number' => $this->nullIfEmpty($p['sk_number'] ?? null),
                        'sk_date' => $this->nullIfEmpty($p['sk_date'] ?? null),
                        'sk_issuer' => $this->nullIfEmpty($p['sk_issuer'] ?? null),
                        'sk_document' => $this->replaceSkToken($p['sk_document'] ?? null, $skFiles),
                        'sk_document_name' => $p['sk_document_name'] ?? null,
                        'position_history' => $history,
                    ];
    
                    if ($assignment) {
                        $assignment->update($attributes);
                    } else {
                        $assignment = StaffAssignment::query()->create($attributes + ['is_signer' => false]);
                    }
    
                    $claimed[] = $assignment->id;
                    if ($signature) $signatures[$person->name] = $signature;
                }
            }
    
            // Yang dihapus admin tidak dihapus dari database: penugasannya ditutup
            // sehingga menjadi riwayat ("pejabat terdahulu").
            $yesterday = now()->subDay()->toDateString();
            $existing->reject(fn ($a) => in_array($a->id, $claimed, true))
                ->each(fn ($a) => $a->update(['end_date' => $yesterday]));
    
            // Modul surat masih membaca tanda tangan dari tabel staff lama (baris is_signer = 1).
            foreach ($signatures as $name => $url) {
                Staff::query()->where('is_signer', true)->where('name', $name)->update(['signature_image' => $url]);
            }
    
            $this->deleteMedia($this->collectMediaUrls($obsoleteMedia, 'profile/organization/'));
            $this->deleteMedia($this->collectMediaUrls($obsoleteMedia, 'profile/signatures/'));
        });
    }
    
    private function personPayload(StaffAssignment $a): array
    {
        $p = $a->person;
    
        return [
            'staff_id' => $p->id,
            'assignment_id' => $a->id,
            'name' => $p->name,
            'title' => $a->position,
            'desc' => $a->description,
            'start_year' => $a->start_date?->year,
            'end_year' => $a->end_date?->year,
            'start_date' => $a->start_date?->format('Y-m-d'),
            'end_date' => $a->end_date?->format('Y-m-d'),
            'term_end_date' => $a->term_end_date?->format('Y-m-d'),
            'photo' => $p->photo,
            'signature' => $p->signature_image,
            'sk_number' => $a->sk_number,
            'sk_date' => $a->sk_date?->format('Y-m-d'),
            'sk_issuer' => $a->sk_issuer,
            'sk_document' => $a->sk_document,
            'sk_document_name' => $a->sk_document_name,
            'position_history' => $a->position_history ?? [],
            'rt_number' => $a->rt?->number,
            'rw_number' => $a->rw?->number ?? $a->rt?->rw?->number,
            'padukuhan' => $a->padukuhan?->name ?? $a->rw?->region?->name ?? $a->rt?->rw?->region?->name,
            'assignments' => $p->historyPayload(),
        ] + $p->bioPayload();
    }
    
    private function structurePayload(?string $title = null, ?string $status = null): array
    {
        $settings = OrganizationalStructureSetting::query()->first();
    
        $levels = StaffAssignment::query()->active()
            ->where(fn ($query) => $query
                ->whereNotIn('position', ['Ketua RT', 'Ketua RW'])
                ->orWhereNotNull('rw_id')
                ->orWhereNotNull('rt_id'))
            ->with([
                'person.assignments.rt.rw.region',
                'person.assignments.rw.region',
                'person.assignments.padukuhan',
                'rt.rw.region',
                'rw.region',
                'padukuhan',
            ])
            ->orderBy('id')->get()
            ->groupBy(fn (StaffAssignment $a) => $this->levelOf($a->position))
            ->sortBy(fn ($group, $level) => array_search($level, self::LEVEL_ORDER, true))
            ->map(fn ($group, $level) => [
                'level' => $level,
                'pimpinan' => $level === 'Lurah',
                'slider' => str_contains($level, 'Dukuh') || str_contains($level, 'Staff'),
                'centerOnDesktop' => $level === 'Staff Pamong Kalurahan',
                'people' => $group
                    ->sortBy(fn (StaffAssignment $a) => $this->positionSortKey($a->position, (int) $a->id))
                    ->map(fn (StaffAssignment $a) => $this->personPayload($a))
                    ->values()
                    ->all(),
            ])->values()->all();
    
        return [
            'organizational_structure_id' => 1,
            'title' => $title ?? 'Struktur Organisasi Pemerintah Kalurahan',
            'levels' => $levels,
            'former_officials' => $settings?->former_officials ?? [],
            'show_former_officials' => (bool) ($settings?->show_former_officials ?? false),
            'status' => $status ?? 'Draft',
            'published_at' => null,
        ];
    }

    private function decodeJsonField(Request $request, string $field): void
    {
        $value = $request->input($field);
        if (!is_string($value)) return;
        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages([$field => "{$field} harus berupa JSON array yang valid."]);
        }
        $request->merge([$field => $decoded]);
    }

    private function replacePhotoToken(?string $photo, array $files): ?string
    {
        if (!$photo || !str_starts_with($photo, 'upload:')) return $photo;
        $token = substr($photo, 7);
        $file = $files[$token] ?? null;
        if (!$file) throw ValidationException::withMessages(['photos' => "File foto untuk token {$token} tidak ditemukan."]);
        return Storage::disk('public')->url($file->store('profile/organization', 'public'));
    }

    private function replaceSignatureToken(?string $signature, array $files): ?string
    {
        if (!$signature || !str_starts_with($signature, 'upload-signature:')) return $signature;
        $token = substr($signature, 17);
        $file = $files[$token] ?? null;
        if (!$file) throw ValidationException::withMessages(['signatures' => "File tanda tangan untuk token {$token} tidak ditemukan."]);
        return Storage::disk('public')->url($file->store('profile/signatures', 'public'));
    }

    private function collectMediaUrls(array $files, string $directory): array
    {
        return array_values(array_filter($files, fn ($file) => is_string($file) && str_contains($file, '/storage/' . trim($directory, '/') . '/')));
    }

    private function deleteMedia(array $photos): void
    {
        foreach ($photos as $url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', (string) parse_url($url, PHP_URL_PATH)));
        }
    }

    private function saveSettings(array $payload): void
    {
        $settings = OrganizationalStructureSetting::query()->first() ?? new OrganizationalStructureSetting();

        if (array_key_exists('former_officials', $payload)) {
            $settings->former_officials = array_values($payload['former_officials'] ?? []);
        }
        if (array_key_exists('show_former_officials', $payload)) {
            $settings->show_former_officials = (bool) $payload['show_former_officials'];
        }

        $settings->save();
    }
}
