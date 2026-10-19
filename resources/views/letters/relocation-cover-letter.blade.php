@extends('letters.layouts.form')

@section('title', 'Surat Pengantar Permohonan Pindah WNI')

@php($kodeForm = 'F.1-25')

@section('content')
    @php
        // ---- Normalisasi isian Pindah WNI: kode angka kotak + tabel keluarga ----
        $relFirst = function (array $keys) use ($form) {
            foreach ($keys as $k) {
                if (isset($form[$k]) && !is_array($form[$k]) && trim((string) $form[$k]) !== '') return trim((string) $form[$k]);
            }
            return '';
        };
        $relNorm = fn ($s) => preg_replace('/[^a-z0-9]/', '', strtolower(preg_replace('/^\s*\d+\s*[.)\-:]\s*/', '', (string) $s)));
        // Nilai select bisa "2. Pendidikan", "2", atau "Pendidikan" -> selalu jadi angka pilihan (kosong bila belum dipilih)
        $relCode = function ($value, array $labels) use ($relNorm) {
            $value = trim((string) $value);
            if ($value === '') return '';
            if (preg_match('/^\s*(\d+)\s*(?:[.)\-:]|$)/', $value, $m)) return $m[1];
            $n = $relNorm($value);
            foreach ($labels as $i => $label) {
                $ln = $relNorm($label);
                if ($n === $ln || ($ln === 'lainnyasebutkan' && str_starts_with($n, 'lainnya'))) return (string) ($i + 1);
            }
            return '';
        };
        // Cari nilai di kunci mana pun yang namanya cocok pola (nama field backend bisa Indonesia/Inggris, snake/camel)
        $relFind = function (string $include, string $exclude = '') use ($form) {
            foreach ($form as $k => $v) {
                if (is_array($v) || trim((string) $v) === '') continue;
                $n = preg_replace('/[^a-z0-9]/', '', strtolower((string) $k));
                if (preg_match($include, $n) && ($exclude === '' || !preg_match($exclude, $n))) return trim((string) $v);
            }
            return '';
        };
        // Alamat asal/tujuan: $form['origin']['x'] atau origin_x / originX
        $relGroup = function (string $group, string $key) use ($form) {
            $studly = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            foreach ([$form[$group][$key] ?? null, $form[$group . '_' . $key] ?? null, $form[$group . $studly] ?? null] as $v) {
                if (!is_array($v) && trim((string) $v) !== '') return trim((string) $v);
            }
            return '';
        };
        $relReasonLabels = ['Pekerjaan', 'Pendidikan', 'Keamanan', 'Kesehatan', 'Perumahan', 'Keluarga', 'Lainnya (sebutkan)'];
        $relTypeLabels = ['Kep. Keluarga', 'Kep. Keluarga dan seluruh Angg. Keluarga', 'Kep. Keluarga dan sbg Angg. Keluarga', 'Anggota Keluarga'];
        $relKkLabels = ['Numpang KK', 'Membuat KK Baru', 'Nomor KK Tetap'];

        // Tabel "Keluarga Yang Pindah": cari daftar baris di kunci mana pun, tahan format kunci (camelCase/snake_case/Indonesia)
        $relRows = null;
        foreach (['family_members', 'familyMembers', 'keluarga_yang_pindah', 'anggota_keluarga_yang_pindah', 'anggota_keluarga'] as $k) {
            if (isset($form[$k])) { $relRows = $form[$k]; break; }
        }
        if ($relRows === null && isset($family_members)) $relRows = $family_members;
        if ($relRows === null) {
            foreach ($form as $v) {
                if (is_string($v) && str_starts_with(ltrim($v), '[{')) $v = json_decode($v, true);
                if (is_array($v) && $v !== [] && is_array(reset($v)) && (isset(reset($v)['nik']) || isset(reset($v)['name']) || isset(reset($v)['nama']))) { $relRows = $v; break; }
            }
        }
        if (is_string($relRows)) $relRows = json_decode($relRows, true) ?: [];
        $relDate = function ($v) {
            $v = trim((string) $v);
            return preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ? date('d-m-Y', strtotime(substr($v, 0, 10))) : $v;
        };
        $relFamily = collect(is_array($relRows) ? $relRows : [])
            ->filter(fn ($r) => is_array($r))
            ->map(fn ($r) => [
                'nik'         => $r['nik'] ?? $r['NIK'] ?? '',
                'name'        => $r['name'] ?? $r['nama'] ?? '',
                'valid_until' => $relDate($r['valid_until'] ?? $r['validUntil'] ?? $r['masa_berlaku'] ?? $r['masa_berlaku_ktp'] ?? ''),
                'shdk'        => $r['shdk'] ?? $r['SHDK'] ?? '',
            ])
            ->filter(fn ($r) => trim(implode('', $r)) !== '')
            ->values()->take(7)->all();
        $relFamily = array_pad($relFamily, 7, ['nik' => '', 'name' => '', 'valid_until' => '', 'shdk' => '']);

        $rel = [
            'reason'      => $relCode($relFirst(['relocation_reason_code', 'relocation_reason', 'relocationReason', 'alasan_pindah']) ?: $relFind('/alasan|reason/', '/lain|other/'), $relReasonLabels),
            'type'        => $relCode($relFirst(['relocation_type_code', 'relocation_type', 'relocationType', 'jenis_kepindahan']) ?: $relFind('/jenis(kepindahan|pindah)|relocationtype|movetype/', '/label/'), $relTypeLabels),
            'origin_kk'   => $relCode($relFirst(['origin_kk_status_code', 'origin_kk_status', 'originKkStatus', 'status_kk_bagi_yang_tidak_pindah']) ?: $relFind('/originkkstatus|kk.*tidakpindah|tidakpindah.*kk/'), $relKkLabels),
            'dest_kk'     => $relCode($relFirst(['destination_kk_status_code', 'destination_kk_status', 'destinationKkStatus', 'status_nomor_kk_bagi_yang_pindah']) ?: $relFind('/destinationkkstatus|statusnomorkk|kk.*(bagi|untuk)?yangpindah/', '/tidak/'), $relKkLabels),
            'reason_other'=> $relFirst(['relocation_reason_other', 'relocationReasonOther', 'alasan_pindah_lainnya']),
            'origin_phone'      => preg_replace('/\D/', '', $relGroup('origin', 'phone')),
            'destination_phone' => preg_replace('/\D/', '', $relGroup('destination', 'phone')),
            'family'      => $relFamily,
        ];
        $rel['reason_other'] = $rel['reason_other'] !== '' ? $rel['reason_other'] : '.....................';
        // Data pemohon & KK asal: utamakan $applicant / $form['origin'], cadangannya isian form datar (kunci snake_case/camelCase)
        $relPick = function (array $sources) {
            foreach ($sources as $v) { if (!is_array($v) && trim((string) $v) !== '') return trim((string) $v); }
            return '';
        };
        $rel['kk_number']   = $relPick([$form['origin']['kk_number'] ?? null, $form['kk_number'] ?? null, $form['kkNumber'] ?? null, $form['origin_kk_number'] ?? null, $form['originKkNumber'] ?? null]);
        $rel['head']        = $relPick([$form['origin']['head_of_family'] ?? null, $form['head_of_family'] ?? null, $form['headOfFamily'] ?? null, $form['origin_head_of_family'] ?? null, $form['originHeadOfFamily'] ?? null]);
        $rel['nik']         = $relPick([$applicant['nik'] ?? null, $form['nik'] ?? null]);
        $rel['name']        = $relPick([$applicant['name'] ?? null, $form['name'] ?? null, $form['full_name'] ?? null]);
        $rel['birth_place'] = $relPick([$applicant['birth_place'] ?? null, $form['birth_place'] ?? null, $form['birthPlace'] ?? null]);
        $rel['birth_date']  = $relDate($relPick([$applicant['birth_date'] ?? null, $form['birth_date'] ?? null, $form['birthDate'] ?? null]));
        $rel['birth']       = $relPick([$rel['birth_place'] !== '' || $rel['birth_date'] !== '' ? trim($rel['birth_place'] . '   ' . $rel['birth_date']) : null, $applicant['birth'] ?? null]);
    @endphp
    @include('letters.region-grid', ['showHamlet' => true])


    <div class="judul-form">Surat Pengantar Permohonan Pindah WNI</div>
    <div class="subjudul-form">{{ $form['relocation_type_label'] ?? 'Antar Desa Dalam Satu Kecamatan' }}</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:135pt;">Nomor Kartu Keluarga</td><td class="f-box">{{ $rel['kk_number'] }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $rel['head'] }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">3.</td>
            <td style="width:135pt; font-size:9.5pt;">Alamat</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $relGroup('origin', 'address') }}</td>
            <td style="width:26pt; font-size:9.5pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('origin', 'rt') }}</td>
            <td style="width:28pt; font-size:9.5pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('origin', 'rw') }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $relGroup('origin', 'hamlet') }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $relGroup('origin', 'village') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('origin', 'regency') }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $relGroup('origin', 'district') }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('origin', 'province') }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt; width:80pt;">{{ $relGroup('origin', 'postal_code') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $rel['origin_phone'][$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no">4.</td><td style="width:135pt;">NIK Pemohon</td><td class="f-box">{{ $rel['nik'] }}</td></tr>
        <tr>
            <td class="f-no">5.</td><td>Tempat/Tanggal Lahir</td>
            <td class="f-box">{{ $applicant['birth_place'] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $applicant['birth_date'] ?? '' }}</td>
        </tr>
        <tr><td class="f-no">6.</td><td>Nama Lengkap</td><td class="f-box">{{ $rel['name'] }}</td></tr>
    </table>

    <div class="sub-heading">DATA KEPINDAHAN</div>

    {{-- 1. Alasan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt; vertical-align:top;">1.</td>
            <td style="width:135pt; font-size:9.5pt; vertical-align:top;">Alasan Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $rel['reason'] }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:23%; font-size:9.5pt; padding:1pt 0;">1. Pekerjaan</td>
                        <td style="width:23%; font-size:9.5pt; padding:1pt 0;">3. Keamanan</td>
                        <td style="width:23%; font-size:9.5pt; padding:1pt 0;">5. Perumahan</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">7. Lainnya (sebutkan)</td>
                    </tr>
                    <tr>
                        <td style="font-size:9.5pt; padding:1pt 0;">2. Pendidikan</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">4. Kesehatan</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">6. Keluarga</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">{{ $rel['reason_other'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 2. Alamat Tujuan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">2.</td>
            <td style="width:135pt; font-size:9.5pt;">Alamat Tujuan Pindah</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $relGroup('destination', 'address') }}</td>
            <td style="width:26pt; font-size:9.5pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('destination', 'rt') }}</td>
            <td style="width:28pt; font-size:9.5pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('destination', 'rw') }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $relGroup('destination', 'hamlet') }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $relGroup('destination', 'village') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('destination', 'regency') }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $relGroup('destination', 'district') }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('destination', 'province') }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt; width:80pt;">{{ $relGroup('destination', 'postal_code') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $rel['destination_phone'][$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    {{-- 3. Jenis Kepindahan --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt; vertical-align:top;">3.</td>
            <td style="width:135pt; font-size:9.5pt; vertical-align:top;">Jenis Kepindahan</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $rel['type'] }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:50%; font-size:9.5pt; padding:1pt 0;">1. Kep. Keluarga</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">3. Kep.Keluarga dan sbg Angg.Keluarga</td>
                    </tr>
                    <tr>
                        <td style="font-size:9.5pt; padding:1pt 0;">2. Kep.Keluarga dan seluruh Angg.Keluarga</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">4. Anggota Keluarga</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">4.</td>
            <td style="width:135pt; font-size:9.5pt;">Status KK bagi yang tidak Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $rel['origin_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">5.</td>
            <td style="font-size:9.5pt;">Status Nomor KK bagi yang Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $rel['dest_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>

    <div class="sub-heading" style="margin-top:2pt;">6. Keluarga Yang Pindah</div>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-size:9pt; margin-top:4pt; page-break-inside: avoid;">
        <tr>
            <th style="border:1px solid #000; width:18pt; padding:1pt 2pt; line-height:1.1;">NO</th>
            <th style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">NIK</th>
            <th style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">NAMA</th>
            <th style="border:1px solid #000; width:60pt; padding:1pt 2pt; line-height:1.1;">MASA BERLAKU KTP S/D</th>
            <th style="border:1px solid #000; width:55pt; padding:1pt 2pt; line-height:1.1;">SHDK</th>
        </tr>
        @foreach ($rel['family'] as $i => $row)
            <tr>
                <td style="border:1px solid #000; padding:1pt 2pt; height:10pt; line-height:1.1;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['nik'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['name'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['valid_until'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>

    <div style="page-break-inside: avoid;">
        <table style="width:100%; border-collapse:collapse; margin-top:4pt;">
            <tr>
                <td style="width:34%;"></td>
                <td style="width:33%; text-align:center; font-size:9.5pt;">{{ $signature['city'] }}</td>
                <td style="width:33%; text-align:right; font-size:9.5pt;">{{ $signature['date'] }}</td>
            </tr>
        </table>
        <table class="ttd-multi" style="page-break-inside: avoid;">
            <tr>
                <td width="34%">Petugas Registrasi,</td>
                <td width="33%">a.n LURAH BIMOMARTANI</td>
                <td width="33%">Pemohon,</td>
            </tr>
            <tr style="page-break-inside: avoid;">
                <td><div class="ttd-space"></div>{{ $form['registration_officer'] ?? '' }}</td>
                <td>
                    @if (!empty($signature['image']))
                        <img class="ttd-image" src="{{ $signature['image'] }}" alt="Tanda Tangan" style="max-height: 45pt; max-width: 120pt; display: block; margin: 0 auto;">
                    @else
                        <div class="ttd-space"></div>
                    @endif
                    {{ $signature['name'] }}
                </td>
                <td><div class="ttd-space"></div>{{ $applicant['name'] ?? '' }}</td>
            </tr>
        </table>
    </div>
@endsection