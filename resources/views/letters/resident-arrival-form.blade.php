@extends('letters.layouts.form')

@section('title', 'Formulir Permohonan Pindah Datang WNI')

@php
    $kodeForm = 'F.1-31';
@endphp

@section('extra_css')
    @page { margin: 0.5cm 1.6cm 0.4cm 1.6cm; }
    body { font-size: 8.6pt; line-height: 1.12; }
    td.f-box { padding: 1pt 3pt; }
    .sub-heading { margin-top: 1.5pt; font-size: 8.6pt; }
    table.frow { margin-top: 0.5pt; }
    table.frow td { font-size: 8.6pt; padding: 0.5pt 0; }
    .judul-form { margin-top: 1.5pt; font-size: 10pt; }
    .subjudul-form { font-size: 8.6pt; }
    table.ttd-multi td { font-size: 8.6pt; vertical-align: top; text-align: center; }
    table.ttd-multi .ttd-space { height: 40pt; }
    table.ttd-multi .ttd-space .signature-image { max-width: 100pt; max-height: 36pt; }
    table.ttd-multi .ttd-name { display: block; margin-top: 3pt; white-space: nowrap; font-size: 7.6pt; }
@endsection

@section('content')
    @php
        // Ambil nilai pertama yang terisi dari beberapa kemungkinan nama key (nested / datar / camelCase / Indonesia)
        $kkPick = function (array $s) {
            foreach ($s as $v) { if (!is_array($v) && trim((string) $v) !== '') return trim((string) $v); }
            return '';
        };
        // KK & kepala keluarga DAERAH ASAL
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
        // KK & kepala keluarga DAERAH TUJUAN
        $destKk = $kkPick([
            $form['destination']['kk_number'] ?? null, $form['destination']['kkNumber'] ?? null, $form['destination']['no_kk'] ?? null,
            $form['destination_kk_number'] ?? null, $form['destinationKkNumber'] ?? null,
        ]);
        $destHead = $kkPick([
            $form['destination']['head_of_family'] ?? null, $form['destination']['headOfFamily'] ?? null, $form['destination']['head_name'] ?? null,
            $form['destination_head_of_family'] ?? null, $form['destinationHeadOfFamily'] ?? null,
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

    @include('letters.region-grid', ['showHamlet' => true])

    <div class="judul-form">Formulir Permohonan Pindah Datang WNI</div>
    <div class="subjudul-form">Antar Kabupaten/Kota Atau Antar Provinsi</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:140pt;">Nomor Kartu keluarga</td><td class="f-box">{{ $originKk }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $originHead }}</td></tr>
        <tr><td class="f-no">3.</td><td>Alamat</td><td class="f-box">{{ $relGroup('origin', 'address') }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; text-align:right; font-size:9.5pt; padding-right:6pt;">RT/RW</td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $relGroup('origin', 'rt') }}</td>
            <td style="width:6pt;"></td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $relGroup('origin', 'rw') }}</td>
            <td></td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('origin', 'village') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kabupaten</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('origin', 'regency') }}</td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('origin', 'district') }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('origin', 'province') }}</td>
        </tr>
        <tr>
            <td style="width:155pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt; width:80pt;">{{ $relGroup('origin', 'postal_code') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">Telepon</td>
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
        <tr><td class="f-no">4.</td><td style="width:140pt;">NIK Pemohon</td><td class="f-box">{{ $applicant['nik'] ?? '' }}</td></tr>
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
        <tr><td class="f-no">6.</td><td>Nama Lengkap Pemohon</td><td class="f-box">{{ $applicant['name'] ?? '' }}</td></tr>
    </table>

    <div class="sub-heading">DATA DAERAH TUJUAN</div>
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">1.</td>
            <td style="width:140pt; font-size:9.5pt;">Status KK Bagi Yang Pindah</td>
            <td style="border:1px solid #000; padding:3pt 4pt;">
                <span class="opsi-box">{{ $relCodes['dest_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>
    <table class="frow">
        <tr><td class="f-no">2.</td><td style="width:140pt;">Nomor Kartu keluarga</td><td class="f-box">{{ $destKk }}</td></tr>
        <tr><td class="f-no">3.</td><td>NIK Pemohon</td><td class="f-box">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr><td class="f-no">4.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $destHead }}</td></tr>
        <tr><td class="f-no">5.</td><td>Tanggal Kedatangan</td><td class="f-box">{{ $relDate($relFirst(['arrival_date', 'arrivalDate'])) }}</td></tr>
        <tr><td class="f-no">6.</td><td>Alamat yang Dituju</td><td class="f-box">{{ $relGroup('destination', 'address') }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; text-align:right; font-size:9.5pt; padding-right:6pt;">RT</td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $relGroup('destination', 'rt') }}</td>
            <td style="width:6pt; text-align:right; font-size:9.5pt;">RW</td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $relGroup('destination', 'rw') }}</td>
            <td></td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('destination', 'village') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kabupaten</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('destination', 'regency') }}</td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $relGroup('destination', 'district') }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $relGroup('destination', 'province') }}</td>
        </tr>
        <tr>
            <td style="width:155pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt; width:80pt;">{{ $relGroup('destination', 'postal_code') }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $relDigits($relGroup('destination', 'phone'))[$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    <div class="sub-heading">6. Keluarga Yang Datang</div>
    <table class="tbl-bordered">
        <tr>
            <th style="width:20pt;">No</th><th>NIK</th><th>Nama</th><th style="width:70pt;">Masa Berlaku KTP</th><th style="width:60pt;">SHDK</th>
        </tr>
        @php
            $familyMembers = is_array($form['family_members'] ?? null) ? $form['family_members'] : (is_string($form['family_members'] ?? null) && str_starts_with(trim($form['family_members'] ?? ''), '[') ? (json_decode($form['family_members'], true) ?? []) : []);
        @endphp
        {{-- Jumlah baris mengikuti data yang diinput (minimal 1 baris). Tinggi baris dirapatkan bila anggota banyak agar tetap 1 lembar. --}}
        @php
            $fmRows = array_values(array_filter($familyMembers, 'is_array'));
            $fmTotal = max(1, count($fmRows));
            $fmH = $fmTotal <= 5 ? 15 : ($fmTotal <= 8 ? 13 : 11);
        @endphp
        @for ($i = 0; $i < $fmTotal; $i++)
            @php
                $row = $fmRows[$i] ?? [];
            @endphp
            <tr>
                <td style="height:{{ $fmH }}pt; text-align:center;">{{ $i + 1 }}</td><td>{{ $row['nik'] ?? '' }}</td><td>{{ $row['name'] ?? '' }}</td>
                <td>{{ $relDate($row['valid_until'] ?? $row['validUntil'] ?? '') }}</td><td>{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:5pt;">
        <tr>
            <td style="width:34%;"></td>
            <td style="width:33%;"></td>
            <td style="width:33%; text-align:right; font-size:9.5pt;">{{ $signature['city'] }} &nbsp;&nbsp; {{ $signature['date'] }}</td>
        </tr>
    </table>
    <table class="ttd-multi">
        <tr>
            <td width="34%">Petugas Registrasi</td>
            <td width="33%">Mengetahui<br>a.n LURAH BIMOMARTANI</td>
            <td width="33%">Pemohon</td>
        </tr>
        <tr>
            <td><div class="ttd-space"></div><span class="ttd-name">{{ $form['registration_officer'] ?? '' }}</span></td>
            <td>
                <div class="ttd-space">
                    @include('letters.partials.signature-image')
                </div>
                <span class="ttd-name">{{ $signature['name'] }}</span>
            </td>
            <td><div class="ttd-space"></div><span class="ttd-name">{{ $applicant['name'] ?? '' }}</span></td>
        </tr>
    </table>

    <p style="margin-top:5pt; font-size:8.5pt;">
        Keterangan :<br>
        *) Diisi Oleh Petugas<br>
        - Formulir ini diisi di Desa
    </p>
@endsection