<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Numpang Nikah</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 9pt; line-height: 1.3; color:#000; }
        p { margin: 0; }
        .kop-block table { border-collapse: collapse; }
        .kop-block td.lbl { width: 130pt; font-weight:bold; }
        .kop-block td.sep { width: 12pt; font-weight:bold; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-top: 14pt; margin-bottom: 12pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.no { width: 16pt; }
        td.lbl { width: 175pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }
        p.gap-top { margin-top: 10pt; }
        .signature-block { margin-top: 18pt; padding-left: 330pt;}
        .signature-block div { line-height: 16pt; }
    </style>
</head>
<body>

    <div class="kop-block">
        <table>
            <tr><td class="lbl">KANTOR DESA/KELURAHAN</td><td class="sep">:</td><td>{{ $village['name'] ?? '' }}</td></tr>
            <tr><td class="lbl">KECAMATAN</td><td class="sep">:</td><td>{{ $village['subdistrict'] ?? '' }}</td></tr>
            <tr><td class="lbl">KABUPATEN/KOTA</td><td class="sep">:</td><td>{{ $village['regency'] ?? '' }}</td></tr>
        </table>
    </div>

    <div class="title">SURAT KETERANGAN NUMPANG NIKAH</div>

    <p>Bersama ini kami sampaikan, bahwa warga kami tersebut dibawah ini :</p>
    <table class="data">
        <tr><td class="no">1</td><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $applicant['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2</td><td class="lbl">Bin/Binti</td><td class="sep">:</td><td class="val">{{ $applicant['bin_or_binti'] ?? '' }}</td></tr>
        <tr><td class="no">3</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr><td class="no">4</td><td class="lbl">Tempat dan Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $applicant['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $applicant['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $applicant['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $applicant['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $applicant['address'] ?? '' }}</td></tr>
        <tr><td class="no">9</td><td class="lbl">Pendidikan Terakhir</td><td class="sep">:</td><td class="val">{{ $applicant['last_education'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">Akan melangsungkan pernikahan dengan seorang wanita dibawah ini :</p>
    <table class="data">
        <tr><td class="no">1</td><td class="lbl">Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $spouse['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2</td><td class="lbl">Binti</td><td class="sep">:</td><td class="val">{{ $spouse['binti'] ?? '' }}</td></tr>
        <tr><td class="no">3</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $spouse['nik'] ?? '' }}</td></tr>
        <tr><td class="no">4</td><td class="lbl">Tempat dan Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $spouse['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $spouse['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $spouse['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $spouse['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $spouse['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">Atas perhatiannya kami ucapkan terima kasih.</p>

    <div class="signature-block">
        <div>{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>
        <div>{{ $signature['position'] ?? '' }}</div>
        <div style="height: 34pt;">&nbsp;</div>
        <div>{{ $signature['name'] ?? '' }}</div>
    </div>

</body>
</html>