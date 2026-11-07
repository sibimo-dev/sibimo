@extends('letters.layouts.form')

@section('title', 'Surat Pengantar Permohonan Pindah WNI')

@php
    $kodeForm = 'F.1-25';
@endphp

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
    @include('letters.region-grid', ['showHamlet' => true])


    <div class="judul-form">Surat Pengantar Permohonan Pindah WNI</div>
    <div class="subjudul-form">{{ $form['relocation_type_label'] ?? 'Antar Desa Dalam Satu Kecamatan' }}</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:135pt;">Nomor Kartu Keluarga</td><td class="f-box">{{ $originKk }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $originHead }}</td></tr>
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
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $relGroup('origin', 'hamlet') ?: $relFirst(['hamlet']) }}</td></tr>
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
            <td class="f-box">{{ $applicant['birth_place'] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $applicant['birth_date'] ?? '' }}</td>
        </tr>
        <tr><td class="f-no">6.</td><td>Nama Lengkap</td><td class="f-box">{{ $applicant['name'] ?? '' }}</td></tr>
    </table>

    <div class="sub-heading">DATA KEPINDAHAN</div>

    {{-- 1. Alasan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt; vertical-align:top;">1.</td>
            <td style="width:135pt; font-size:9.5pt; vertical-align:top;">Alasan Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $relCodes['reason'] }}</span>
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
                        <td style="font-size:9.5pt; padding:1pt 0;">{{ $relReasonOther }}</td>
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
                        <td>{{ $relDigits($relGroup('destination', 'phone'))[$i] ?? '' }}</td>
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
                <span class="opsi-box">{{ $relCodes['type'] }}</span>
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
                <span class="opsi-box">{{ $relCodes['origin_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">5.</td>
            <td style="font-size:9.5pt;">Status Nomor KK bagi yang Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $relCodes['dest_kk'] }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>

    <div class="sub-heading" style="margin-top:2pt;">6. Keluarga Yang Pindah</div>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-size:9pt; margin-top:4pt; page-break-inside: avoid;">
        <tr>
            <th style="border:1px solid #000; width:18pt; padding:2pt 2pt; line-height:1.1;">NO</th>
            <th style="border:1px solid #000; padding:2pt 2pt; line-height:1.1;">NIK</th>
            <th style="border:1px solid #000; padding:2pt 2pt; line-height:1.1;">NAMA</th>
            <th style="border:1px solid #000; width:74pt; padding:2pt 2pt; line-height:1.1;">MASA BERLAKU KTP S/D</th>
            <th style="border:1px solid #000; width:55pt; padding:2pt 2pt; line-height:1.1;">SHDK</th>
        </tr>
        {{-- Jumlah baris mengikuti data yang diinput (minimal 1 baris). Tinggi baris dirapatkan bila anggota banyak agar tetap 1 lembar. --}}
        @php
            $fmRaw = $form['family_members'] ?? $form['familyMembers'] ?? [];
            if (is_string($fmRaw)) $fmRaw = json_decode($fmRaw, true) ?: [];
            // Masa berlaku KTP: terima valid_until / validUntil. Tanggal ISO -> d-m-Y; teks (mis. SEUMUR HIDUP) dicetak apa adanya.
            $fmRows = array_values(array_map(
                fn ($r) => array_merge($r, ['valid_until' => $relDate($r['valid_until'] ?? $r['validUntil'] ?? $r['masa_berlaku_ktp'] ?? $r['masa_berlaku'] ?? '')]),
                array_filter(is_array($fmRaw) ? $fmRaw : [], 'is_array')
            ));
            $fmTotal = max(1, count($fmRows));
            $fmH = $fmTotal <= 5 ? 15 : ($fmTotal <= 8 ? 13 : 11);
        @endphp
        @for ($i = 0; $i < $fmTotal; $i++)
            @php
                $row = $fmRows[$i] ?? [];
            @endphp
            <tr>
                <td style="border:1px solid #000; padding:2pt 3pt; height:{{ $fmH }}pt; line-height:1.1; text-align:center;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000; padding:2pt 3pt; height:{{ $fmH }}pt; line-height:1.1;">{{ $row['nik'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:2pt 3pt; height:{{ $fmH }}pt; line-height:1.1;">{{ $row['name'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:2pt 3pt; height:{{ $fmH }}pt; line-height:1.1; white-space:nowrap; font-size:8.5pt;">{{ $row['valid_until'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:2pt 3pt; height:{{ $fmH }}pt; line-height:1.1;">{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @endfor
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
                <td><div class="ttd-space" style="height:42pt;"></div>{{ $form['registration_officer'] ?? '' }}</td>
                <td>
                    <div class="ttd-space" style="height:42pt;">
                        @include('letters.partials.signature-image')
                    </div>
                    {{ $signature['name'] }}
                </td>
                <td><div class="ttd-space" style="height:42pt;"></div>{{ $applicant['name'] ?? '' }}</td>
            </tr>
        </table>
    </div>
@endsection