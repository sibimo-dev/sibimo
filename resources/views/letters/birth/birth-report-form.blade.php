@extends('letters.layouts.form')

@section('title', 'Formulir Pelaporan Kelahiran')

{{-- Mandiri: TIDAK lagi meng-include partial apa pun dari letters.birth.partials.*. --}}
@section('extra_css')
    body { font-family: "Times New Roman", Times, serif; }
    .bf { font-size: 10pt; line-height: 1.15; }
    .bf-title { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; }
    .bf-sub { text-align: center; font-size: 10pt; font-weight: bold; }
    .sec-title { font-weight: bold; margin-top: 5pt; }

    table.rows { width: 100%; border-collapse: collapse; }
    table.rows td { padding: 1.7pt 0; vertical-align: top; }
    table.rows td.r-no { width: 14pt; padding-left: 10pt; }
    table.rows.flat td.r-no { padding-left: 0; }
    table.rows td.r-sep { width: 10pt; }
    table.rows td.r-val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 11pt; }

    .frame { border: 1px solid #000; padding: 1pt 6pt 2pt 6pt; margin-top: 3pt; page-break-inside: avoid; }
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
    // satuan (Tahun/Kg/Cm) selalu tercetak; kalau nilai kosong, satuan digeser sedikit ke kanan
    $withUnit = fn ($v, string $unit) => (filled($v) ? $v . $nb : str_repeat($nb, 12)) . $unit;
    // Formulir ini tidak menampilkan nama kalurahan di baris RT/RW (showVillage = false)
    $villageTail = '';

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

<div class="bf">

    <div class="bf-title" style="font-style: italic; margin-top: 6pt;">FORMULIR PELAPORAN KELAHIRAN</div>
    <div class="bf-sub" style="font-style: italic;">( Untuk Mendapatkan Akta Kelahiran )</div>
    <div class="bf-title" style="font-style: italic; margin-top: 1pt;">PEMERINTAH KABUPATEN SLEMAN</div>

    <div style="margin-top: 10pt;">
        {!! $renderRows([
            ['label' => 'No. KK', 'value' => $birth['family_card_number']],
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
            ['label' => 'Kelahiran Ke', 'value' => $c['birth_order']],
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
            ['label' => 'Nama Lengkap', 'value' => $m['name']],
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

    <div class="sec-title" style="font-size: 8.5pt;">DATA ADMINISTRASI (di isi oleh petugas)</div>
    {!! $renderRows([
        ['label' => 'Jenis Pelaporan', 'value' => $birth['report_kind']],
        ['label' => 'Nama Petugas Register Desa', 'value' => $birth['registrar_name']],
    ], 130) !!}

    <div style="font-style: italic; margin-top: 8pt;">*) coret yang tidak perlu</div>

</div>
@endsection