<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Belum Pernah Menikah</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 10.5pt; line-height: 1.3; color:#000; }
        p { margin: 0; }
        .kop-block table { border-collapse: collapse; }
        .kop-block td.lbl { width: 130pt; font-weight:bold; }
        .kop-block td.sep { width: 12pt; font-weight:bold; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-top: 14pt; }
        .number { text-align:center; margin-bottom: 12pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.no { width: 16pt; }
        td.lbl { width: 165pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }
        p.gap-top { margin-top: 10pt; }
        .signature-block { margin-top: 18pt; text-align: right; padding-right: 25pt; }
        .signature-block div:nth-child(2) {position: relative;left: -100pt;}
        .center-signer { width:71%; vertical-align:top; }
    </style>
</head>
<body>

    <div class="kop-block">
        <table>
            <tr><td class="lbl">KANTOR KALURAHAN</td><td class="sep">:</td><td>{{ $village['name'] ?? '' }}</td></tr>
            <tr><td class="lbl">KAPANEWON</td><td class="sep">:</td><td>{{ $village['subdistrict'] ?? '' }}</td></tr>
            <tr><td class="lbl">KABUPATEN/KOTA</td><td class="sep">:</td><td>{{ $village['regency'] ?? '' }}</td></tr>
        </table>
    </div>

    <div class="title">SURAT KETERANGAN BELUM PERNAH MENIKAH</div>
    <div class="number">Nomor: {{ $number ?? '' }}</div>

    <p>Saya, yang bertanda tangan di bawah ini :</p>
    <table class="data">
        <tr><td class="no">1.</td><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $signature['name'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">Jabatan</td><td class="sep">:</td><td class="val">{{ $signature['position'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">dengan ini menerangkan bahwa :</p>
    <table class="data">
        <tr><td class="no">1.</td><td class="lbl">Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $applicant['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $applicant['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Jenis kelamin</td><td class="sep">:</td><td class="val">{{ $applicant['gender'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $applicant['religion'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $applicant['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Tempat tinggal</td><td class="sep">:</td><td class="val">{{ $applicant['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">
        pada saat Surat Keterangan Belum Pernah Menikah ini diterbitkan, yang bersangkutan
        benar-benar BELUM PERNAH MENIKAH.
    </p>
    <p class="gap-top">Demikianlah surat keterangan ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>

    <div class="signature-block">
        <div>{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>
        <div>{{ $signature['position'] ?? '' }}</div>
        <div style="height: 34pt;">&nbsp;</div>
        <div>{{ $signature['name'] ?? '' }}</div>
    </div>

</body>
</html>