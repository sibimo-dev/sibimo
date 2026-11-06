@extends('letters.layouts.form')

@section('title', 'Formulir Keterangan Pindah WNI')

@php
    $kodeForm = 'F.1-26';
@endphp

@section('extra_css')
    @page { margin: 0.6cm 1.5cm 0.4cm 1.5cm; }
    body { font-size: 8.5pt; line-height: 1.05; }
    table.wgrid td.wg-label { font-size: 8.5pt; }
    table.wgrid table.wg-kode td { height: 9pt; font-size: 8pt; }
    table.wgrid td.wg-val { padding: 2pt 4pt; }
    table.frow { margin-top: 0.5pt; }
    table.frow td { font-size: 8.5pt; padding: 0.3pt 0; }
    td.f-box { padding: 2pt 4pt; }
    table.digit-box-full td { height: 9pt; }
    .sub-heading { margin-top: 2pt; }
    .judul-form { margin-top: 1pt; font-size: 9.5pt; }
    .subjudul-form { font-size: 8pt; }
    .ttd-left .ttd-space { height: 16pt; }
    table.kop-dukcapil { width: 100%; border-collapse: collapse; }
    table.kop-dukcapil td { padding: 0; vertical-align: middle; }
    table.kop-dukcapil td.kop-logo { width: 62pt; }
    table.kop-dukcapil td.kop-logo img { width: 52pt; height: 68pt; }
    table.kop-dukcapil td.kop-text { text-align: center; padding-right: 62pt; }
    .kopd-1 { font-size: 10.5pt; font-weight: bold; line-height: 13pt; }
    .kopd-2 { font-size: 12.5pt; font-weight: bold; line-height: 15pt; }
    .kopd-info { font-size: 8.5pt; line-height: 1.2; }
    .kopd-web { font-size: 8.5pt; font-weight: bold; line-height: 1.2; }
    .kop-line { border-bottom: 2pt solid #000; margin-top: 3pt; margin-bottom: 2pt; }
    table.frow { border-collapse: separate; border-spacing: 0 2pt; margin-top: 1pt; }
    table.frow td { font-size: 9pt; padding: 0; }
    td.f-box { padding: 1pt 4pt; height: 11pt; }
    table.wgrid { margin-top: 1pt; }
    table.wgrid td.wg-label { font-size: 9pt; }
    table.wgrid table.wg-kode td { height: 10pt; font-size: 8.5pt; }
    table.wgrid td.wg-val { padding: 1pt 4pt; }
    table.digit-box-full td { height: 11pt; }
    .opsi-box { height: 11pt; }
    .sub-heading { margin-top: 2pt; font-size: 9pt; }
    .judul-form { margin-top: 2pt; font-size: 10.5pt; }
    .ttd-left .ttd-space { height: 26pt; }
    table.ttd-left { width: 100%; border-collapse: collapse; margin-top: 2pt; }
    table.ttd-left td { font-size: 9.5pt; vertical-align: top; padding: 0; }
@endsection

@section('content')
    @php
        // Ambil nilai pertama yang terisi dari beberapa kemungkinan nama key (nested / datar / camelCase / Indonesia)
        $kkPick = function (array $s) {
            foreach ($s as $v) { if (!is_array($v) && trim((string) $v) !== '') return trim((string) $v); }
            return '';
        };
        $originKk = $kkPick([
            $form['origin']['kk_number'] ?? null, $form['origin']['kkNumber'] ?? null, $form['origin']['no_kk'] ?? null,
            $form['origin_kk_number'] ?? null, $form['originKkNumber'] ?? null,
            $form['kk_number'] ?? null, $form['kkNumber'] ?? null, $form['no_kk'] ?? null, $form['nomor_kk'] ?? null,
            $applicant['kk_number'] ?? null, $applicant['kk'] ?? null,
        ]);
        $originHead = $kkPick([
            $form['origin']['head_of_family'] ?? null, $form['origin']['headOfFamily'] ?? null, $form['origin']['head_name'] ?? null,
            $form['origin_head_of_family'] ?? null, $form['originHeadOfFamily'] ?? null,
            $form['head_of_family'] ?? null, $form['headOfFamily'] ?? null, $form['kepala_keluarga'] ?? null, $form['nama_kepala_keluarga'] ?? null,
            $applicant['head_of_family'] ?? null,
        ]);
    @endphp
    @php
        // ---- Normalisasi isian pindah: kode angka kotak, telepon, tanggal, alamat (nested / datar / camelCase) ----
        $relFirst = fn (array $keys) => collect($keys)->map(fn ($k) => $form[$k] ?? null)->first(fn ($v) => !is_array($v) && trim((string) $v) !== '') ?? '';
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
        $relDate = function ($v) {
            $v = trim((string) $v);
            return preg_match('/^\d{4}-\d{2}-\d{2}/', $v) ? date('d-m-Y', strtotime(substr($v, 0, 10))) : $v;
        };
        $relDigits = fn ($v) => preg_replace('/\D/', '', (string) $v); // telepon: 1 digit = 1 kotak
        // Urutan label HARUS sama dengan OPT.relocationReason / relocationType / kkStatusNo di letterFields.js
        $relReasonLabels = ['Pekerjaan', 'Pendidikan', 'Keamanan', 'Kesehatan', 'Perumahan', 'Keluarga', 'Lainnya (sebutkan)'];
        $relTypeLabels = ['Kep. Keluarga', 'Kep. Keluarga dan seluruh Angg. Keluarga', 'Kep. Keluarga dan sbg Angg. Keluarga', 'Anggota Keluarga'];
        $relKkLabels = ['Numpang KK', 'Membuat KK Baru', 'Nomor KK Tetap'];
        $relCodes = [
            'reason'    => $relCode($relFirst(['relocation_reason_code', 'relocation_reason', 'relocationReason', 'alasan_pindah']) ?: $relFind('/alasan|reason/', '/lain|other/'), $relReasonLabels),
            'type'      => $relCode($relFirst(['relocation_type_code', 'relocation_type', 'relocationType', 'jenis_kepindahan']) ?: $relFind('/jenis(kepindahan|pindah)|relocationtype|movetype/', '/label/'), $relTypeLabels),
            'origin_kk' => $relCode($relFirst(['origin_kk_status_code', 'origin_kk_status', 'originKkStatus', 'status_kk_bagi_yang_tidak_pindah']) ?: $relFind('/originkkstatus|kk.*tidakpindah|tidakpindah.*kk/'), $relKkLabels),
            'dest_kk'   => $relCode($relFirst(['destination_kk_status_code', 'destination_kk_status', 'destinationKkStatus', 'status_nomor_kk_bagi_yang_pindah']) ?: $relFind('/destinationkkstatus|statusnomorkk|kk.*(bagi|untuk)?yangpindah/', '/tidak/'), $relKkLabels),
        ];
        $relReasonOther = $relFirst(['relocation_reason_other', 'relocationReasonOther', 'alasan_pindah_lainnya']) ?: '.....................';
    @endphp
    @php
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
    @endphp
    {{-- ===== KOP DUKCAPIL ===== --}}
    <table class="kop-dukcapil">
        <tr>
            <td class="kop-logo"><img src="{{ $logo }}" alt="Logo Kabupaten Sleman"></td>
            <td class="kop-text">
                <div class="kopd-1">PEMERINTAH KABUPATEN SLEMAN</div>
                <div class="kopd-2">DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</div>
                <div class="kopd-info">Jalan KRT Pringgodiningrat No. 3 Beran, Tridadi, Sleman , DIY, 55511</div>
                <div class="kopd-info">Telepon ( 0274 ) 868362, Faksimile ( 0274 ) 868945</div>
                <div class="kopd-web">website : www.capil.slemankab.go.id, E-mail : dukcapil@slemankab.go.id</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    @include('letters.region-grid', ['showHamlet' => true])

    <div class="judul-form">Formulir Keterangan Pindah WNI</div>
    <div class="subjudul-form">{{ $form['relocation_type_label'] ?? 'Antar Desa Dalam Satu Kecamatan' }}</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:135pt;">Nomor Kartu Keluarga</td><td class="f-box">{{ $originKk }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $originHead }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt;">3.</td>
            <td style="width:135pt; font-size:9pt;">Alamat</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('origin', 'address') }}</td>
            <td style="width:26pt; font-size:9pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('origin', 'rt') }}</td>
            <td style="width:28pt; font-size:9pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('origin', 'rw') }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $relGroup('origin', 'hamlet') ?: $relFirst(['hamlet']) }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:separate; border-spacing:0 2pt; margin-top:1pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('origin', 'village') }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('origin', 'regency') }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('origin', 'district') }}</td>
            <td style="font-size:9pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('origin', 'province') }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt; width:80pt;">{{ $relGroup('origin', 'postal_code') }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $relDigits($relGroup('origin', 'phone'))[$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no">4.</td><td style="width:135pt;">NIK Pemohon</td><td class="f-box">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr>
            <td class="f-no">5.</td><td>Tempat/Tanggal Lahir</td>
            <td class="f-box">
                @if (!empty($applicant['birth_place']) || !empty($applicant['birth_date']))
                    {{ $applicant['birth_place'] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $applicant['birth_date'] ?? '' }}
                @else
                    {{ $applicant['birth'] ?? '' }}
                @endif
            </td>
        </tr>
        <tr><td class="f-no">6.</td><td>Nama Lengkap</td><td class="f-box">{{ $applicant['name'] ?? '' }}</td></tr>
    </table>

    <div class="sub-heading">DATA KEPINDAHAN</div>

    {{-- 1. Alasan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt; vertical-align:top;">1.</td>
            <td style="width:135pt; font-size:9pt; vertical-align:top;">Alasan Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $relCodes['reason'] }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:23%; font-size:9pt; padding:1pt 0;">1. Pekerjaan</td>
                        <td style="width:23%; font-size:9pt; padding:1pt 0;">3. Keamanan</td>
                        <td style="width:23%; font-size:9pt; padding:1pt 0;">5. Perumahan</td>
                        <td style="font-size:9pt; padding:1pt 0;">7. Lainnya (sebutkan)</td>
                    </tr>
                    <tr>
                        <td style="font-size:9pt; padding:1pt 0;">2. Pendidikan</td>
                        <td style="font-size:9pt; padding:1pt 0;">4. Kesehatan</td>
                        <td style="font-size:9pt; padding:1pt 0;">6. Keluarga</td>
                        <td style="font-size:9pt; padding:1pt 0;">{{ $relReasonOther }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 2. Alamat Tujuan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt;">2.</td>
            <td style="width:135pt; font-size:9pt;">Alamat Tujuan Pindah</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('destination', 'address') }}</td>
            <td style="width:26pt; font-size:9pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('destination', 'rt') }}</td>
            <td style="width:28pt; font-size:9pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $relGroup('destination', 'rw') }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $relGroup('destination', 'hamlet') }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:separate; border-spacing:0 2pt; margin-top:1pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('destination', 'village') }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('destination', 'regency') }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('destination', 'district') }}</td>
            <td style="font-size:9pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('destination', 'province') }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt; width:80pt;">{{ $relGroup('destination', 'postal_code') }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $relDigits($relGroup('destination', 'phone'))[$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    {{-- 3. Jenis Kepindahan --}}
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt; vertical-align:top;">3.</td>
            <td style="width:135pt; font-size:9pt; vertical-align:top;">Jenis Kepindahan</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $relCodes['type'] }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:50%; font-size:9pt; padding:1pt 0;">1. Kep. Keluarga</td>
                        <td style="font-size:9pt; padding:1pt 0;">3. Kep.Keluarga dan sbg Angg.Keluarga</td>
                    </tr>
                    <tr>
                        <td style="font-size:9pt; padding:1pt 0;">2. Kep.Keluarga dan seluruh Angg.Keluarga</td>
                        <td style="font-size:9pt; padding:1pt 0;">4. Anggota Keluarga</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:separate; border-spacing:0 2pt; margin-top:1pt;">
        <tr>
            <td style="width:15pt; font-size:9pt;">4.</td>
            <td style="width:135pt; font-size:9pt;">Status KK bagi yang tidak Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $relCodes['origin_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
        <tr>
            <td style="font-size:9pt;">5.</td>
            <td style="font-size:9pt;">Status Nomor KK bagi yang Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $relCodes['dest_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>

    <div class="sub-heading" style="margin-top:2pt;">6. Keluarga Yang Pindah</div>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-size:9pt; margin-top:4pt; page-break-inside: avoid;">
        <tr>
            <th style="border:1px solid #000; width:18pt; padding:1.5pt 2pt; line-height:1.05;">NO</th>
            <th style="border:1px solid #000; padding:1.5pt 2pt; line-height:1.05;">NIK</th>
            <th style="border:1px solid #000; padding:1.5pt 2pt; line-height:1.05;">NAMA</th>
            <th style="border:1px solid #000; width:74pt; padding:1.5pt 2pt; line-height:1.05;">MASA BERLAKU KTP S/D</th>
            <th style="border:1px solid #000; width:55pt; padding:1.5pt 2pt; line-height:1.05;">SHDK</th>
        </tr>
        {{-- Jumlah baris mengikuti data yang diinput (minimal 1 baris). Tinggi baris dirapatkan bila anggota banyak agar tetap 1 lembar. --}}
        @php
            $fmRows = collect(is_array($relRows) ? $relRows : [])->filter(fn ($r) => is_array($r))
                ->map(fn ($r) => [
                    'nik'         => $r['nik'] ?? $r['NIK'] ?? '',
                    'name'        => $r['name'] ?? $r['nama'] ?? '',
                    'valid_until' => $relDate($r['valid_until'] ?? $r['validUntil'] ?? $r['masa_berlaku'] ?? $r['masa_berlaku_ktp'] ?? ''),
                    'shdk'        => $r['shdk'] ?? $r['SHDK'] ?? '',
                ])
                ->filter(fn ($r) => trim(implode('', $r)) !== '')
                ->values()->all();
            $fmTotal = max(1, count($fmRows));
            $fmH = $fmTotal <= 5 ? 13 : ($fmTotal <= 8 ? 12 : 11);
        @endphp
        @for ($i = 0; $i < $fmTotal; $i++)
            @php
                $row = $fmRows[$i] ?? [];
            @endphp
            <tr>
                <td style="border:1px solid #000; padding:1.5pt 3pt; height:{{ $fmH }}pt; line-height:1.05; text-align:center;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000; padding:1.5pt 3pt; height:{{ $fmH }}pt; line-height:1.05;">{{ $row['nik'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1.5pt 3pt; height:{{ $fmH }}pt; line-height:1.05;">{{ $row['name'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1.5pt 3pt; height:{{ $fmH }}pt; line-height:1.05; white-space:nowrap; font-size:8pt;">{{ $row['valid_until'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1.5pt 3pt; height:{{ $fmH }}pt; line-height:1.05;">{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    {{-- ===== TTD: rata kiri di separuh kanan halaman, sesuai formulir asli ===== --}}
    <div style="page-break-inside: avoid;">
        <table class="ttd-left">
            <tr>
                <td style="width:50%;"></td>
                <td>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="font-size:9pt;">{{ $signature['city'] }}</td>
                            <td style="font-size:9pt; text-align:right;">{{ $signature['date_long'] }}</td>
                        </tr>
                    </table>
                    <div>Dikeluarkan Oleh</div>
                    <div>a.n Kepala Dinas Kependudukan Dan Pencatatan Sipil Kabupaten Sleman</div>
                    <div>Lurah</div>
                    <div class="ttd-space">
                        @include('letters.partials.signature-image')
                    </div>
                    <div>{{ $signature['name'] }}</div>
                </td>
            </tr>
        </table>
    </div>
@endsection