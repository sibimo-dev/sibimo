@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Kelahiran')

{{--
    Mandiri: TIDAK lagi meng-include partial apa pun dari letters.birth.partials.*.
    .judul / .nomor / .kop-line / table.sign berasal dari letters.layouts.base sendiri
    (bukan partial birth) -- di sini tetap DITIMPA (override) supaya kop + seluruh data
    tetap muat satu halaman folio, sama seperti sebelumnya.
--}}
@section('extra_css')
    body { font-family: "Times New Roman", Times, serif; }

    /* Dipadatkan supaya kop + seluruh data muat satu halaman folio (override atas
       default letters.layouts.base) */
    .judul { margin-left: 0; margin-top: 2pt; font-size: 11pt; }
    .nomor { font-size: 9.5pt; margin-top: 1pt; }
    .kop-line { margin-top: 4pt; }
    .sign-space { height: 16pt; }

    .bf { font-size: 8.6pt; line-height: 0.98; }

    table.rows { width: 100%; border-collapse: collapse; }
    table.rows td { padding: 0.3pt 0; vertical-align: top; }
    table.rows td.r-no { width: 14pt; padding-left: 10pt; }
    table.rows.flat td.r-no { padding-left: 0; }
    table.rows td.r-sep { width: 10pt; }
    table.rows td.r-val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 7.5pt; }

    .frame { border: 1px solid #000; margin-top: 1pt; padding: 0 5pt 0 5pt; page-break-inside: avoid; }
    .frame-title { font-weight: bold; }

    table.split { width: 100%; border-collapse: collapse; }
    table.split td { width: 50%; vertical-align: top; }
    table.split td.gap-r { padding-right: 10pt; }
    table.split td.gap-l { padding-left: 10pt; }
@endsection

@section('content')
@php
    $c = $birth['child'];
    $m = $birth['mother'];
    $f = $birth['father'];
    $r = $birth['reporter'];
    $mar = $birth['marriage'];
    $w = $birth['witnesses'];

    $nb = "\u{00A0}";
    $placeDate = fn (array $p) => ($p['birth_place'] ?? '') . ' / ' . ($p['birth_date'] ?? '');
    $withUnit = fn ($v, string $unit) => (filled($v) ? $v . $nb : str_repeat($nb, 12)) . $unit;
    // Surat ini menampilkan nama kalurahan di baris RT/RW (showVillage = true)
    $villageTail = str_repeat($nb, 3) . $village;

    // ===== Pengganti partial 'rows'.
    $renderRows = function (array $rows, int $lw = 140, bool $flat = false) {
        $n = 1;
        $cls = 'rows' . ($flat ? ' flat' : '');
        $html = '<table class="' . $cls . '">';
        foreach ($rows as $row) {
            $bare = ! empty($row['cont']);
            $numbered = ! $bare && empty($row['nonum']);
            $no = $numbered ? ($n++) . '.' : '';
            $sep = $bare ? '' : ':';
            $label = e($row['label'] ?? '');
            $value = e($row['value'] ?? '');
            $html .= '<tr>'
                . '<td class="r-no">' . $no . '</td>'
                . '<td class="r-lbl" style="width: ' . $lw . 'pt;">' . $label . '</td>'
                . '<td class="r-sep">' . $sep . '</td>'
                . '<td class="r-val">' . $value . '</td>'
                . '</tr>';
        }
        return $html . '</table>';
    };
@endphp

    <div class="judul">SURAT KETERANGAN KELAHIRAN</div>
    <div class="nomor">NO : {{ $number }}</div>

    <div class="bf" style="margin-top: 2pt;">

        <div class="frame">
            {!! $renderRows([
                ['label' => 'Nomer Kartu Keluarga', 'value' => $birth['family_card_number']],
                ['label' => 'Nama Kepala Keluarga', 'value' => $birth['head_of_family_name']],
            ]) !!}
        </div>

        {{-- ===== BAYI / ANAK ===== --}}
        <div class="frame">
            <div class="frame-title">BAYI/ANAK</div>
            {!! $renderRows([
                ['label' => 'NIK', 'value' => $c['nik']],
                ['label' => 'Nama Lengkap', 'value' => $c['name']],
                ['label' => 'Jenis Kelamin', 'value' => $c['gender']],
                ['label' => 'Tempat Dilahirkan', 'value' => $c['delivery_place']],
                ['label' => 'Tempat Kelahiran', 'value' => $c['birth_place']],
                ['label' => 'Hari/Tgl,Bln,Th', 'value' => trim(($c['birth_day_name'] ?? '') . ' / ' . ($c['birth_date'] ?? ''))],
                ['label' => 'Waktu /Jam Kelahiran', 'value' => filled($c['birth_time']) ? $c['birth_time'] . ' WIB' : ''],
                ['label' => 'Jenis Kelahiran', 'value' => $c['plurality']],
                ['label' => 'Kelahiran ke (dng huruf)', 'value' => $c['birth_order']],
                ['label' => 'Penolong Kelahiran', 'value' => $c['birth_attendant']],
                ['label' => 'Berat Bayi', 'value' => $withUnit($c['weight'], 'Kg')],
                ['label' => 'Panjang Bayi', 'value' => $withUnit($c['length'], 'Cm')],
            ]) !!}
        </div>

        {{-- ===== IBU ===== --}}
        <div class="frame">
            <div class="frame-title">IBU</div>
            {!! $renderRows([
                ['label' => 'NIK', 'value' => $m['nik']],
                ['label' => 'Nama Lengkap (Sesuai Buku Nikah)', 'value' => $m['name']],
                ['label' => 'Tempat/Tanggal Lahir', 'value' => $placeDate($m)],
                ['label' => 'Pekerjaan', 'value' => $m['occupation']],
                ['label' => 'Alamat', 'value' => $m['address']],
                ['cont' => true, 'label' => '', 'value' => $m['rt_rw'] . $villageTail],
                ['label' => 'Kewarganegaraan', 'value' => $m['nationality']],
                ['label' => 'Tgl Pencatatan Perkawinan', 'value' => ($mar['record_place'] ?? '') . str_repeat($nb, 14) . 'Tanggal : ' . ($mar['record_date'] ?? '')],
            ]) !!}
        </div>

        {{-- ===== AYAH ===== --}}
        <div class="frame">
            <div class="frame-title">AYAH</div>
            {!! $renderRows([
                ['label' => 'NIK', 'value' => $f['nik']],
                ['label' => 'Nama Lengkap', 'value' => $f['name']],
                ['label' => 'Tempat/Tanggal Lahir', 'value' => $placeDate($f)],
                ['label' => 'Pekerjaan', 'value' => $f['occupation']],
                ['label' => 'Alamat', 'value' => $f['address']],
                ['cont' => true, 'label' => '', 'value' => $f['rt_rw'] . $villageTail],
                ['label' => 'Kewarganegaraan', 'value' => $f['nationality']],
            ]) !!}
        </div>

        {{-- ===== PELAPOR ===== --}}
        <div class="frame">
            <div class="frame-title">PELAPOR</div>
            {!! $renderRows([
                ['label' => 'NIK', 'value' => $r['nik']],
                ['label' => 'Nama Lengkap', 'value' => $r['name']],
                ['label' => 'Tempat/Tanggal Lahir', 'value' => $placeDate($r)],
                ['label' => 'Umur', 'value' => $withUnit($r['age'], 'Tahun')],
                ['label' => 'Pekerjaan', 'value' => $r['occupation']],
                ['label' => 'Alamat', 'value' => $r['address']],
                ['label' => 'Tanggal Lapor', 'value' => $r['report_date']],
            ]) !!}
        </div>

        {{-- ===== SAKSI I | SAKSI II ===== --}}
        <div class="frame">
            <table class="split">
                <tr>
                    <td class="gap-r">
                        <div class="frame-title">SAKSI I</div>
                        {!! $renderRows([
                            ['label' => 'NIK', 'value' => $w[0]['nik']],
                            ['label' => 'Nama Lengkap', 'value' => $w[0]['name']],
                            ['label' => 'Umur', 'value' => $withUnit($w[0]['age'], 'Tahun')],
                            ['label' => 'Alamat', 'value' => $w[0]['address']],
                        ], 68) !!}
                    </td>
                    <td class="gap-l">
                        <div class="frame-title">SAKSI II</div>
                        {!! $renderRows([
                            ['label' => 'NIK', 'value' => $w[1]['nik']],
                            ['label' => 'Nama Lengkap', 'value' => $w[1]['name']],
                            ['label' => 'Umur', 'value' => $withUnit($w[1]['age'], 'Tahun')],
                            ['label' => 'Alamat', 'value' => $w[1]['address']],
                        ], 68) !!}
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection

{{-- TTD dua kolom: menggantikan blok TTD standar di layouts.base --}}
@section('custom_signature')
    <table class="sign" style="width: 100%; border-collapse: collapse; page-break-inside: avoid; margin-top: 2pt; font-size: 8.6pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                Mengetahui
                @foreach ($signature['prefix'] as $line)
                    <br>{{ $line }}
                @endforeach
                <br>{{ $signature['position'] }}
            </td>
            <td style="width: 50%; vertical-align: top;">
                {{ $signature['city'] }}, {{ $signature['date_long'] }}
                <br>Pelapor
            </td>
        </tr>
        <tr>
            <td><div class="sign-space"></div>{{ $signature['name'] }}</td>
            <td><div class="sign-space"></div>{{ $birth['reporter']['name'] }}</td>
        </tr>
    </table>
@endsection
