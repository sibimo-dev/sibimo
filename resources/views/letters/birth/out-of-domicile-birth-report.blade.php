{{--
    Laporan Kelahiran Luar Domisili. Standalone (tanpa kop, tanpa @extends layout).
    SENGAJA TIDAK memakai @include ke partial mana pun (styles / rows / report-sections) --
    semua CSS dan logika render baris ditulis langsung di file ini, supaya file ini
    berdiri sendiri sepenuhnya. Data yang dipakai: $birth, $village, $signature.
--}}
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
    $villageTail = str_repeat($nb, 3) . $village;

    // ===== Pengganti partial 'rows': render tabel "No. Label : Nilai" dari array baris.
    //   $rows  : array of ['label' => ..., 'value' => ..., 'nonum' => bool, 'cont' => bool]
    //              - nonum : baris tanpa nomor (tetap ada titik dua)
    //              - cont  : baris lanjutan tanpa nomor & tanpa titik dua (mis. baris RT/RW)
    //   $lw    : lebar kolom label dalam pt
    //   $flat  : true -> nomor TIDAK menjorok (rata kiri), mis. baris No. KK / Nama Kepala Keluarga
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kelahiran Luar Domisili</title>
    <style>
        @page { margin: 1.5cm 2cm 1.3cm 2cm; } 
        body { margin: 0; color: #000; font-family: "Times New Roman", Times, serif; }

        .bf { font-size: 10pt; line-height: 1.15; }
        .bf-title { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; }

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

        table.foot { width: 100%; border-collapse: collapse; margin-top: 8pt; page-break-inside: avoid; }
        table.foot td { width: 50%; vertical-align: top; text-align: center; padding: 0; }
        table.foot td.foot-space { height: 60pt; }
        .foot-name { display: inline-block; min-width: 130pt; border-bottom: 1px dashed #000; }
    </style>
</head>
<body class="bf">
    <div class="bf-title" style="margin-bottom: 10pt;">Laporan Kelahiran Luar Domisili</div>

    {!! $renderRows([
        ['nonum' => true, 'label' => 'No. KK', 'value' => $birth['family_card_number']],
        ['nonum' => true, 'label' => 'Nama Kepala Keluarga', 'value' => $birth['head_of_family_name']],
    ], 140, true) !!}

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
            ['label' => 'Anak ke ( dng huruf )', 'value' => $c['birth_order']],
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

    {{-- ===== TANDA TANGAN ===== --}}
    <table class="foot">
        <tr>
            <td>&nbsp;</td>
            <td>{{ $signature['city'] }}, {{ $r['report_date'] ?: '..........................' }}</td>
        </tr>
        <tr>
            <td>Mengetahui</td>
            <td>Pelapor</td>
        </tr>
        <tr>
            <td>Dukuh {{ $birth['hamlet']['name'] }}</td>
            <td></td>
        </tr>
        <tr>
            <td class="foot-space"></td>
            <td class="foot-space"></td>
        </tr>
        <tr>
            <td><span class="foot-name">{{ $birth['hamlet']['head_name'] }}</span></td>
            <td><span class="foot-name">{{ $r['name'] }}</span></td>
        </tr>
    </table>
</body>
</html>