<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Persetujuan Calon Pengantin</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 10.5pt; line-height: 1.3; color:#000; }
        p { margin: 0; }
        .form-ref { text-align:right; font-size: 8.5pt; line-height: 1.2; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-top: 14pt; margin-bottom: 12pt; }
        .party-title { font-weight:bold; margin-top: 8pt; margin-bottom: 2pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 2pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.no { width: 16pt; }
        td.lbl { width: 175pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }
        p.gap-top { margin-top: 10pt; }
        .signature-heading {text-align: right; margin-top: 16pt;}
        table.sig-cols { width: 100%; border-collapse: collapse; margin-top: 4pt;}
        table.sig-cols td { width: 50%; vertical-align: top;}
        table.sig-cols tr:first-child td:nth-child(2) { text-indent: 90pt;}
        .sig-space { height: 40pt;}
    </style>
</head>
<body>

    <div class="form-ref">
        Lampiran VIII Keputusan Dirjen Bimas Islam<br>
        Nomor 473 Tahun 2020 tentang<br>
        Petunjuk Teknis Pelaksanaan Pencatatan Nikah<br><br>
        Model N4
    </div>

    <div class="title">PERSETUJUAN CALON PENGANTIN</div>

    <p>Yang bertanda tangan di bawah ini :</p>

    <div class="party-title">A. Calon Suami :</div>
    <table class="data">
        <tr><td class="no">1.</td><td class="lbl">Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $groom['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">Bin</td><td class="sep">:</td><td class="val">{{ $groom['bin'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $groom['nik'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $groom['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $groom['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $groom['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $groom['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $groom['address'] ?? '' }}</td></tr>
    </table>

    <div class="party-title">B. Calon Istri :</div>
    <table class="data">
        <tr><td class="no">1.</td><td class="lbl">Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $bride['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">Binti</td><td class="sep">:</td><td class="val">{{ $bride['binti'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $bride['nik'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $bride['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $bride['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $bride['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $bride['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $bride['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">
        Menyatakan dengan sesungguhnya bahwa atas dasar suka rela, dengan kesadaran sendiri, tanpa
        ada paksaan dari siapapun juga, setuju untuk melangsungkan pernikahan.
    </p>
    <p class="gap-top">Demikian surat persetujuan ini dibuat untuk digunakan seperlunya.</p>

    <div class="signature-heading">{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>

    <table class="sig-cols">
        <tr>
            <td>Calon Suami</td>
            <td>Calon Istri</td>
        </tr>
        <tr>
            <td><div class="sig-space">&nbsp;</div></td>
            <td><div class="sig-space">&nbsp;</div></td>
        </tr>
        <tr>
            <td>{{ $groom['full_name_alias'] ?? '' }}</td>
            <td>{{ $bride['full_name_alias'] ?? '' }}</td>
        </tr>
    </table>

</body>
</html>