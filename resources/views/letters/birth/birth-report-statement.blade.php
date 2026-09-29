@extends('letters.layouts.form')

@section('title', 'Laporan Kelahiran')

{{--
    Mandiri: TIDAK lagi meng-include partial apa pun dari letters.birth.partials.*.
    Semua CSS dan render baris ("No. Label : Nilai") ditulis langsung di file ini.
    @extends('letters.layouts.form') tetap dipakai karena itu layout dasar bersama
    (bukan bagian dari partial birth), sama seperti late-birth-registration-approval-decree
    yang tetap @extends('letters.layouts.base').
--}}
@section('extra_css')
    body { font-family: "Times New Roman", Times, serif; }
    .bf { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.15; }
    .bf-title { text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; }

    table.rows { width: 100%; border-collapse: collapse; }
    table.rows td { padding-top: 2pt; padding-bottom: 2pt; vertical-align: top; }
    table.rows td.r-no { width: 14pt; padding-left: 10pt; }
    table.rows.flat td.r-no { padding-left: 0; }
    table.rows td.r-sep { width: 10pt; }
    table.rows td.r-val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 11pt; }

    table.sign { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
    table.sign td { width: 50%; vertical-align: top; padding: 0 14pt; line-height: 1.2; }
    .sign-space { height: 30pt; }
@endsection

@section('content')
@php
    $c = $birth['child'];
    $m = $birth['mother'];
    $f = $birth['father'];
    $r = $birth['reporter'];
    $nb = "\u{00A0}";
    $withUnit = fn ($v, string $unit) => filled($v) ? $v . ' ' . $unit : '';
    $villageTail = str_repeat($nb, 3) . $village;

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

<div class="bf">

    <div class="bf-title" style="text-decoration: underline; margin-top: 6pt;">LAPORAN KELAHIRAN</div>

    <p style="margin-top: 25pt;">Yang bertanda tangan dibawah ini saya</p>

    {!! $renderRows([
        ['label' => 'NIK', 'value' => $r['nik']],
        ['label' => 'Nama Lengkap', 'value' => $r['name']],
        ['label' => 'Tanggal Lahir', 'value' => $r['birth_date']],
        ['label' => 'Umur', 'value' => $withUnit($r['age'], 'Tahun')],
        ['label' => 'Pekerjaan', 'value' => $r['occupation']],
        ['label' => 'Alamat', 'value' => $r['address']],
    ], 100) !!}

    {{-- "Hubungan dengan si bayi": table 1 baris (label + garis titik-titik yang melebar),
         rata kiri sama seperti paragraf "Dengan ini melaporkan..." di bawahnya. --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 10pt;">
        <tr>
            <td style="white-space: nowrap; padding: 0 4pt 0 0; vertical-align: top;">Hubungan dengan si bayi :</td>
            <td style="width: 100%; border-bottom: 1px dashed #000; padding: 0;">{{ $r['relationship'] }}</td>
        </tr>
    </table>

    <p style="margin-top: 8pt;">Dengan ini melaporkan kelahiran seorang anak :</p>

    {!! $renderRows([
        ['nonum' => true, 'label' => 'Dari Ibu', 'value' => $m['name']],
        ['nonum' => true, 'label' => 'Alamat Ibu', 'value' => $m['address']],
        ['cont' => true, 'label' => '', 'value' => $m['rt_rw'] . $villageTail],
        ['nonum' => true, 'label' => 'Suami Dari', 'value' => $f['name']],
        ['nonum' => true, 'label' => 'Alamat Ayah', 'value' => $f['address']],
        ['cont' => true, 'label' => '', 'value' => $f['rt_rw'] . $villageTail],
    ], 70) !!}

    <p style="margin-top: 10pt;">Dengan data kelahiran sebagai berikut</p>

    {!! $renderRows([
        ['label' => 'Nama Lengkap Anak', 'value' => $c['name']],
        ['label' => 'Jenis Kelamin', 'value' => $c['gender']],
        ['label' => 'Tempat Dilahirkan', 'value' => $c['delivery_place']],
        ['label' => 'Alamat RS/RB', 'value' => $c['delivery_address']],
        ['label' => 'Tempat Kelahiran', 'value' => $c['birth_place']],
        ['label' => 'Hari/Tgl,Bln,Th', 'value' => trim(($c['birth_day_name'] ?? '') . ' / ' . ($c['birth_date'] ?? ''))],
        ['label' => 'Waktu /Jam Kelahiran', 'value' => filled($c['birth_time']) ? $c['birth_time'] . ' WIB' : ''],
        ['label' => 'Jenis Kelahiran', 'value' => $c['plurality']],
        ['label' => 'Kelahiran Ke', 'value' => $c['birth_order']],
        ['label' => 'Penolong Kelahiran', 'value' => $c['birth_attendant']],
        ['label' => 'Berat Bayi', 'value' => $withUnit($c['weight'], 'Kg')],
        ['label' => 'Panjang Bayi', 'value' => $withUnit($c['length'], 'Cm')],
        ['label' => 'Umur Kelahiran', 'value' => $c['gestational_age']],
        ['label' => 'Cara Kelahiran', 'value' => $c['delivery_method']],
        ['label' => 'Biaya Kelahiran', 'value' => $c['delivery_cost']],
    ], 120) !!}

    <p style="margin-top: 10pt; text-align: justify;">
        Demikian laporan saya ini saya buat dengan sebenar-benarnya dan disertai bukti dan dokumen
        pendukung pelaporan kelahiran, apabila laporan saya ini dikemudian hari diketahui tidak benar
        maka saya siap dituntut sesuai peraturan perundang-undangan yang berlaku.
    </p>

    <table class="sign" style="margin-top: 10pt;">
        <tr>
            <td></td>
            <td>{{ $signature['city'] }}, {{ $r['report_date'] ?? '..........................' }}</td>
        </tr>
        <tr>
            <td>Mengetahui<br>Dukuh {{ $birth['hamlet']['name'] }}</td>
            <td>Pelapor</td>
        </tr>
        <tr>
            <td><div class="sign-space"></div>{{ $birth['hamlet']['head_name'] }}</td>
            <td><div class="sign-space"></div>{{ $r['name'] }}</td>
        </tr>
    </table>

</div>
@endsection