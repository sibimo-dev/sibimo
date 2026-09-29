<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Kematian</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 9pt; line-height: 1.3; color:#000; }
        p { margin: 0; }
        .form-ref { text-align:right; font-size: 8.5pt; line-height: 1.2; }
        .kop-block { margin-top: 8pt; }
        .kop-block table { border-collapse: collapse; }
        .kop-block td.lbl { width: 130pt; font-weight:bold; }
        .kop-block td.sep { width: 12pt; font-weight:bold; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-top: 12pt; }
        .number { text-align:center; margin-bottom: 10pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.section { width: 16pt; }
        td.no { width: 12pt; }
        td.lbl { width: 175pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }
        p.gap-top { margin-top: 10pt; }
        .signature-block { margin-top: 18pt; text-align: right; padding-right: 25pt; }
        .signature-block div:nth-child(2) { position: relative; left: -86pt; }
        .center-signer { width:71%; vertical-align:top; }
    </style>
</head>
<body>

    <div class="form-ref">
        Lampiran X Keputusan Dirjen Bimas Islam<br>
        Nomor 473 Tahun 2020 tentang<br>
        Petunjuk Teknis Pelaksanaan Pencatatan Nikah<br><br>
        Model N6
    </div>

    <div class="kop-block">
        <table>
            <tr><td class="lbl">KANTOR DESA/KELURAHAN</td><td class="sep">:</td><td>{{ $village['name'] ?? '' }}</td></tr>
            <tr><td class="lbl">KECAMATAN</td><td class="sep">:</td><td>{{ $village['subdistrict'] ?? '' }}</td></tr>
            <tr><td class="lbl">KABUPATEN/KOTA</td><td class="sep">:</td><td>{{ $village['regency'] ?? '' }}</td></tr>
        </table>
    </div>

    <div class="title">SURAT KETERANGAN KEMATIAN</div>
    <div class="number">Nomor : {{ $number ?? '' }}</div>

    <p>Yang bertanda tangan dibawah ini menerangkan dengan sesungguhnya bahwa :</p>

    <table class="data">
        <tr><td class="section">A.</td><td class="no">1.</td><td class="lbl">Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $deceased['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">2.</td><td class="lbl">Binti</td><td class="sep">:</td><td class="val">{{ $deceased['binti'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $deceased['nik'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $deceased['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $deceased['nationality'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $deceased['religion'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $deceased['occupation'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $deceased['address'] ?? '' }}</td></tr>
    </table>

    <table class="data">
        <tr><td class="section"></td><td class="no"></td><td class="lbl">Telah meninggal dunia pada</td><td class="sep">:</td><td class="val">{{ $deceased['death_date'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no"></td><td class="lbl">Di</td><td class="sep">:</td><td class="val">{{ $deceased['death_place'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">Yang bersangkutan adalah Suami/Istri dari :</p>

    <table class="data">
        <tr><td class="section">B.</td><td class="no">1.</td><td class="lbl">Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $spouse['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">2.</td><td class="lbl">Bin</td><td class="sep">:</td><td class="val">{{ $spouse['bin'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $spouse['nik'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $spouse['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $spouse['nationality'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $spouse['religion'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $spouse['occupation'] ?? '' }}</td></tr>
        <tr><td class="section"></td><td class="no">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $spouse['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">
        Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk digunakan
        seperlunya.
    </p>

    <div class="signature-block">
        <div>{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>
        <div>{{ $signature['position'] ?? '' }}</div>
        <div style="height: 34pt;">&nbsp;</div>
        <div>{{ $signature['name'] ?? '' }}</div>
    </div>

</body>
</html>