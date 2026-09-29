<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Parental Consent Letter</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 10.3pt; line-height: 1.28; color:#000; }
        p { margin: 0; }
        .form-ref { text-align:right; font-size: 8.5pt; line-height: 1.2; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-top: 14pt; margin-bottom: 12pt; }
        .party-title { font-weight:bold; margin-top: 6pt; margin-bottom: 2pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 2pt; }
        table.data td { padding: 1.2pt 0; vertical-align:top; }

        
        td.no { width: 16pt; padding-left: 100pt; }
        td.no.wide { padding-left: 4pt; }
        td.no.wide.indent { padding-left: 20pt; }

        table.data.couple-data td.no {
            padding-left: 20pt;
        }

        td.lbl { width: 165pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 12pt; }
        p.gap-top { margin-top: 8pt; }
        .signature-heading { text-align:left; margin-top: 14pt; margin-left: 60%; }
        table.sig-cols { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.sig-cols td { width:50%; vertical-align:top; }
        table.sig-cols td.sig-right { padding-left: 50pt; }
        .sig-space { height: 36pt; }
    </style>
</head>
<body>

    <div class="form-ref">
        Lampiran IX Keputusan Dirjen Bimas Islam<br>
        Nomor 473 Tahun 2020 tentang<br>
        Petunjuk Teknis Pelaksanaan Pencatatan Nikah<br><br>
        Model N5
    </div>

    <div class="title">SURAT IZIN ORANG TUA</div>

    <p>Yang bertanda tangan di bawah ini :</p>

    <table class="data">
        <tr><td class="no wide">A. 1.</td><td class="lbl"><b>Nama lengkap dan alias</b></td><td class="sep">:</td><td class="val">{{ $father['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">2.</td><td class="lbl">Bin</td><td class="sep">:</td><td class="val">{{ $father['bin'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $father['nik'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $father['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $father['nationality'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $father['religion'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $father['occupation'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $father['address'] ?? '' }}</td></tr>

        <tr><td class="no wide">B. 1.</td><td class="lbl"><b>Nama lengkap dan alias</b></td><td class="sep">:</td><td class="val">{{ $mother['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">2.</td><td class="lbl">Binti</td><td class="sep">:</td><td class="val">{{ $mother['binti'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $mother['nik'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $mother['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $mother['nationality'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $mother['religion'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $mother['occupation'] ?? '' }}</td></tr>
        <tr><td class="no wide indent">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $mother['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">Adalah ayah dan ibu kandung/wali/pengampu dari :</p>
    <table class="data couple-data">
        <tr><td class="no">1.</td><td class="lbl"><b>Nama lengkap dan alias</b></td><td class="sep">:</td><td class="val">{{ $child['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">Bin/Binti</td><td class="sep">:</td><td class="val">{{ $child['bin_or_binti'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $child['nik'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $child['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $child['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $child['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $child['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $child['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">Memberikan izin kepada anak kami untuk melakukan pernikahan dengan:</p>
    <table class="data couple-data">
        <tr><td class="no">1.</td><td class="lbl"><b>Nama lengkap dan alias</b></td><td class="sep">:</td><td class="val">{{ $child_spouse['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">Bin/Binti</td><td class="sep">:</td><td class="val">{{ $child_spouse['bin_or_binti'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $child_spouse['nik'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $child_spouse['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $child_spouse['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $child_spouse['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $child_spouse['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $child_spouse['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">
        Demikian surat izin ini dibuat dengan kesadaran tanpa ada paksaan dari siapapun dan untuk
        digunakan seperlunya.
    </p>

    <div class="signature-heading">{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>

    <table class="sig-cols">
        <tr>
            <td>Ayah/Wali/Pengampu</td>
            <td class="sig-right">Ibu/Wali/Pengampu</td>
        </tr>
        <tr>
            <td><div class="sig-space">&nbsp;</div></td>
            <td class="sig-right"><div class="sig-space">&nbsp;</div></td>
        </tr>
        <tr>
            <td>{{ $father['full_name_alias'] ?? '' }}</td>
            <td class="sig-right">{{ $mother['full_name_alias'] ?? '' }}</td>
        </tr>
    </table>

</body>
</html>