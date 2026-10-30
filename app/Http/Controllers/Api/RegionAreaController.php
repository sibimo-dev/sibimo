<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\StaffAssignment;
use App\Models\StaffPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;



class RegionAreaController extends Controller
{
    // Pohon dusun -> RW -> RT. Dipakai form data warga, dialog admin, dan halaman publik.
    public function index(Request $request): JsonResponse
    {
        $isAdmin = (bool) $request->user('sanctum');
        $regions = Region::query()
            ->with([
                'headStaff',
                'rws' => fn ($query) => $query->orderBy('number'),
                'rws.rts' => fn ($query) => $query->orderBy('number'),
            ])
            ->orderBy('name')
            ->get();

        $assignments = StaffAssignment::query()->active()
            ->where(fn ($q) => $q->whereIn('position', ['Ketua RW', 'Ketua RT'])->orWhereNotNull('padukuhan_id'))
            ->with(['person.assignments.rt', 'person.assignments.rw'])
            ->get();
        $dukuhHeads = $assignments->whereNotNull('padukuhan_id')->keyBy('padukuhan_id');
        $rwHeads = $assignments->where('position', 'Ketua RW')->whereNotNull('rw_id')->keyBy('rw_id');
        $rtHeads = $assignments->where('position', 'Ketua RT')->whereNotNull('rt_id')->keyBy('rt_id');

        // Biodata + riwayat ketua, dipakai popup detail di halaman publik.
        $detail = fn (?StaffAssignment $a) => $a ? [
            'photo' => $a->person->photo,
            'sk_number' => $a->sk_number,
            'start_date' => $a->start_date?->format('Y-m-d'),
            'term_end_date' => $a->term_end_date?->format('Y-m-d'),
            'position_history' => $a->position_history ?? [],
            'assignments' => $a->person->historyPayload(),
        ] + $a->person->bioPayload() : null;

        $data = $regions->map(fn (Region $region) => [
            'region_id' => $region->region_id,
            'dusun' => $region->name,
            'head' => $dukuhHeads->get($region->region_id)?->person?->name ?? $region->head_name,
            'head_staff_id' => $region->head_staff_id,
            'rws' => $region->rws->map(fn (Rw $rw) => [
                'id' => $rw->id,
                'rw' => $rw->number,
                'head_name' => $rwHeads->get($rw->id)?->person?->name ?? $rw->head_name,
                'head' => $detail($rwHeads->get($rw->id)),
                ...($isAdmin ? ['head_phone' => $rw->head_phone] : []),
                'rts' => $rw->rts->map(fn (Rt $rt) => [
                    'id' => $rt->id,
                    'rt' => $rt->number,
                    'head_name' => $rtHeads->get($rt->id)?->person?->name ?? $rt->head_name,
                    'head' => $detail($rtHeads->get($rt->id)),
                    ...($isAdmin ? ['head_phone' => $rt->head_phone] : []),
                ])->values(),
            ])->values(),
        ])->values();

        return response()->json([
            'success' => true,
            'message' => 'Data dusun, RW, dan RT berhasil diambil.',
            'data' => $data,
        ]);
    }

    private function headRules(): array
    {
        return [
            'head_name' => ['nullable', 'string', 'max:100'],
            'head_phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'education' => ['nullable', 'string', 'max:50'],
            'major' => ['nullable', 'string', 'max:150'],
            'school_name' => ['nullable', 'string', 'max:150'],
            'graduation_year' => ['nullable', 'integer', 'between:1900,2100'],
            'sk_number' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'position_history' => ['nullable', 'array'],
            'position_history.*.sk' => ['nullable', 'string', 'max:200'],
            'position_history.*.date' => ['nullable', 'date'],
            'position_history.*.official' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function updateRw(Request $request, int $id): JsonResponse
    {
        $rw = Rw::findOrFail($id);
        $validated = $request->validate($this->headRules());
        $rw->update(Arr::only($validated, ['head_name', 'head_phone']));
        $this->syncHead('Ketua RW', 'rw_id', $rw->id, $validated);

        return response()->json(['success' => true, 'data' => $rw]);
    }

    public function updateRt(Request $request, int $id): JsonResponse
    {
        $rt = Rt::findOrFail($id);
        $validated = $request->validate($this->headRules());
        $rt->update(Arr::only($validated, ['head_name', 'head_phone']));
        $this->syncHead('Ketua RT', 'rt_id', $rt->id, $validated);

        return response()->json(['success' => true, 'data' => $rt]);
    }

    private function syncHead(string $position, string $column, int $id, array $data): void
    {
        if (!array_key_exists('head_name', $data)) return;

        $name = trim((string) $data['head_name']);
        $phone = trim((string) ($data['head_phone'] ?? '')) ?: null;
        $current = StaffAssignment::query()->active()
            ->where('position', $position)->where($column, $id)->with('person')->first();

        if ($current && mb_strtolower($current->person->name) === mb_strtolower($name)) {
            $current->person->update(['phone' => $phone]);
            $assignment = $current;
        } else {
            $current?->update(['end_date' => now()->subDay()->toDateString()]);
            if ($name === '') return;

            $person = StaffPerson::query()->create(['name' => $name, 'phone' => $phone]);
            $assignment = StaffAssignment::query()->create([
                'staff_id' => $person->id,
                'position' => $position,
                $column => $id,
                'start_date' => today()->toDateString(),
            ]);
            $assignment->setRelation('person', $person);
        }

        $this->saveDetail($assignment, $data);
    }

    // Hanya jalan kalau payload berasal dari dialog Detail (ada key "gender").
    private function saveDetail(StaffAssignment $a, array $d): void
    {
        if (!array_key_exists('gender', $d)) return;

        $blank = fn ($v) => ($v === '' || $v === null) ? null : $v;

        $a->person->update([
            'gender' => $blank($d['gender'] ?? null),
            'birth_date' => $blank($d['birth_date'] ?? null),
            'education' => $blank($d['education'] ?? null),
            'major' => $blank($d['major'] ?? null),
            'school_name' => $blank($d['school_name'] ?? null),
            'graduation_year' => $blank($d['graduation_year'] ?? null),
        ]);

        // "Tanggal selesai" disimpan ke term_end_date, BUKAN end_date.
        // end_date mematikan penugasan (scope active), sehingga ketua hilang dari publik.
        $a->update([
            'start_date' => $blank($d['start_date'] ?? null) ?? $a->start_date,
            'term_end_date' => $blank($d['end_date'] ?? null),
            'sk_number' => $blank($d['sk_number'] ?? null),
            'position_history' => array_values($d['position_history'] ?? []),
        ]);
    }
}