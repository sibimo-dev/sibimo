{{--
    Pelaporan Pencatatan Kelahiran. Standalone (tanpa kop, tanpa @extends layout).
    Mandiri: TIDAK lagi meng-include partial apa pun dari letters.birth.partials.*.
    Data: $birth, $village.
--}}
@php
    $nb = "\u{00A0}";
    $gap = fn (int $n = 8) => str_repeat($nb, $n);
    $city = 'Sleman';

    $c = $birth['child'];
    $m = $birth['mother'];
    $f = $birth['father'];
    $r = $birth['reporter'];
    $mar = $birth['marriage'];
    $w = $birth['witnesses'];

    $withUnit = fn ($v, string $unit) => filled($v) ? $v . ' ' . $unit : '';
    $placeDate = fn (array $p) => ($p['birth_place'] ?? '') . $gap(10) . ($p['birth_date'] ?? '');
    $ageText = fn (array $p) => 'Umur : ' . $withUnit($p['age'] ?? null, 'Tahun');
    $reportDate = $r['report_date'] ?: '..........................';

    // ===== Pengganti partial 'rows': render tabel "No. Label : Nilai" dari array baris.
    //   nonum : baris tanpa nomor (tetap ada titik dua)
    //   cont  : baris lanjutan tanpa nomor & tanpa titik dua (mis. baris RT/RW)
    //   $flat : true -> nomor TIDAK menjorok, mis. baris No. KK / Nama Kepala Keluarga
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
    <title>Pelaporan Pencatatan Kelahiran</title>
    <style>
        @page { margin: 1.3cm 1.5cm; }
        body { margin: 0; color: #000; font-family: "Times New Roman", Times, serif; }
        .bf { font-size: 11pt; }
        .bf-title { text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; }

        table.rows { width: 100%; border-collapse: collapse; }
        table.rows td { padding: 1pt 0; vertical-align: top; }
        table.rows td.r-no { width: 14pt; padding-left: 10pt; }
        table.rows.flat td.r-no { padding-left: 0; }
        table.rows td.r-sep { width: 10pt; }
        table.rows td.r-val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 11pt; }

        .sec-title { font-weight: bold; margin-top: 10pt; }

        table.split { width: 100%; border-collapse: collapse; }
        table.split td { width: 50%; vertical-align: top; }
        table.split td.gap-r { padding-right: 10pt; }
        table.split td.gap-l { padding-left: 10pt; }
        .frame-title { font-weight: bold; }

        table.sign-r { width: 100%; border-collapse: collapse; margin-top: 8pt; page-break-inside: avoid; }
        table.sign-r td { vertical-align: top; }
        .sign-r-space { height: 30pt; }
    </style>
</head>
<body class="bf">
    <div class="bf-title" style="margin-bottom: 16pt;">PELAPORAN PENCATATAN KELAHIRAN</div>

    {!! $renderRows([
        ['nonum' => true, 'label' => 'No. KK', 'value' => $birth['family_card_number']],
        ['nonum' => true, 'label' => 'Nama Kepala Keluarga', 'value' => $birth['head_of_family_name']],
    ], 110, true) !!}

    <div class="sec-title">DATA ANAK</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $c['nik']],
        ['label' => 'Nama Lengkap', 'value' => $c['name']],
        ['label' => 'Jenis Kelamin', 'value' => $c['gender'] . $gap(30) . 'Anak Ke : ' . $c['birth_order']],
        ['label' => 'Tempat Dilahirkan', 'value' => $c['delivery_place']],
        ['label' => 'Tempat Kelahiran', 'value' => 'Kabupaten/Kota' . $gap(6) . $c['birth_place']],
        ['label' => 'Hari/Tanggal Lahir', 'value' => trim(($c['birth_day_name'] ?? '') . $gap(20) . ($c['birth_date'] ?? ''))],
        ['label' => 'Pukul', 'value' => $gap(14) . (filled($c['birth_time']) ? $c['birth_time'] . $gap(4) . 'WIB' : '')],
        ['label' => 'Jenis Kelahiran', 'value' => $c['plurality']],
        ['label' => 'Penolong Kelahiran', 'value' => $c['birth_attendant']],
        ['label' => 'Berat Bayi', 'value' => $withUnit($c['weight'], 'Kg') . $gap(20) . 'Panjang Bayi : ' . $withUnit($c['length'], 'Cm')],
    ], 110) !!}

    <div class="sec-title">DATA IBU KANDUNG</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $m['nik']],
        ['label' => 'Nama Lengkap', 'value' => $m['name']],
        ['label' => 'Tempat/Tanggal Lahir', 'value' => $placeDate($m) . $gap(6) . $ageText($m)],
        ['label' => 'Pekerjaan', 'value' => $m['occupation']],
        ['label' => 'Alamat', 'value' => $m['address']],
        ['cont' => true, 'label' => '', 'value' => $m['rt_rw'] . $gap(4) . $village],
        ['label' => 'Kewarganegaraan', 'value' => $m['nationality'] . $gap(20) . 'Kebangsaan : ' . ($m['ethnicity'] ?? '')],
        ['label' => 'Kawin Sah di', 'value' => 'KUA/Gereja' . $gap(6) . ':' . $gap(4) . ($mar['record_place'] ?? '')],
        ['nonum' => true, 'label' => 'Nomor Akta Nikah/Akta Perkawinan', 'value' => $mar['certificate_number']],
        ['nonum' => true, 'label' => 'Tanggal Akta Nikah/Akta Perkawinan', 'value' => $mar['record_date']],
    ], 110) !!}

    <div class="sec-title">DATA AYAH KANDUNG</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $f['nik']],
        ['label' => 'Nama Lengkap', 'value' => $f['name']],
        ['label' => 'Tempat/Tanggal Lahir', 'value' => $placeDate($f) . $gap(6) . $ageText($f)],
        ['label' => 'Pekerjaan', 'value' => $f['occupation']],
        ['label' => 'Alamat', 'value' => $f['address']],
        ['cont' => true, 'label' => '', 'value' => $f['rt_rw'] . $gap(4) . $village],
        ['label' => 'Kewarganegaraan', 'value' => $f['nationality'] . $gap(20) . 'Kebangsaan : ' . ($f['ethnicity'] ?? '')],
    ], 110) !!}

    <div class="sec-title">DATA PELAPOR</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $r['nik']],
        ['label' => 'Nama Lengkap', 'value' => $r['name']],
        ['label' => 'Tempat/Tanggal Lahir', 'value' => $placeDate($r) . $gap(6) . $ageText($r)],
        ['label' => 'Pekerjaan', 'value' => $r['occupation']],
        ['label' => 'Alamat', 'value' => $r['address'] . $gap(6) . $village],
    ], 110) !!}

    <table class="split" style="margin-top: 8pt; page-break-inside: avoid;">
        <tr>
            <td class="gap-r">
                <div class="frame-title">SAKSI I</div>
                {!! $renderRows([
                    ['label' => 'NIK', 'value' => $w[0]['nik']],
                    ['label' => 'Nama Lengkap', 'value' => $w[0]['name']],
                    ['label' => 'Umur', 'value' => $withUnit($w[0]['age'], 'Tahun')],
                    ['label' => 'Alamat', 'value' => $w[0]['address']],
                ], 58) !!}
            </td>
            <td class="gap-l">
                <div class="frame-title">SAKSI II</div>
                {!! $renderRows([
                    ['label' => 'NIK', 'value' => $w[1]['nik']],
                    ['label' => 'Nama Lengkap', 'value' => $w[1]['name']],
                    ['label' => 'Umur', 'value' => $withUnit($w[1]['age'], 'Tahun')],
                    ['label' => 'Alamat', 'value' => $w[1]['address']],
                ], 58) !!}
            </td>
        </tr>
    </table>

    <table class="sign-r">
        <tr>
            <td style="width: 50%;"></td>
            <td>
                {{ $city }}, {{ $reportDate }}<br>
                Pelapor
                <div class="sign-r-space"></div>
                <b>{{ $r['name'] }}</b>
            </td>
        </tr>
    </table>
</body>
</html>