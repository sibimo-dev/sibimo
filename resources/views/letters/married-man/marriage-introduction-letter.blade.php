{{--
    Pengantar Nikah (Model N1).
    Isi kolom letter_types.blade_view dengan: letters.marriage-introduction-letter

    Data: $number, $village (name, subdistrict, regency),
          $applicant (name, nik, gender, birth_place_date, nationality, religion,
                      occupation, last_education, address, marital_status, previous_spouse_name),
          $father (full_name_alias, nik, birth_place_date, nationality, religion, occupation, address),
          $mother (full_name_alias, nik, birth_place_date, nationality, religion, occupation, address),
          $signature (city, date_long, position, name)
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pengantar Nikah</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 10.5pt; line-height: 1.3; color:#000; }
        p { margin: 0; }
        .form-ref { text-align:right; font-size: 8.5pt; line-height: 1.2; }
        .kop-block { margin-top: 8pt; }
        .kop-block table { border-collapse: collapse; font-size: 9pt; white-space: nowrap; }
        .kop-block td.lbl { width: 115pt; font-weight:bold; white-space: nowrap; }
        .kop-block td.sep { width: 10pt; font-weight:bold; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-top: 12pt; }
        .number { text-align:center; margin-bottom: 12pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.no { width: 16pt; }
        td.lbl { width: 175pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }
        tr.status-row td.lbl { width: auto; }
        p.gap-top { margin-top: 10pt; }
        .signature-block { margin-top: 18pt; margin-left: 62%; }
        .signature-block div { line-height: 16pt; }
    </style>
</head>
<body>

@php
    $maritalStatus = trim($applicant['marital_status'] ?? '');
    $genderLower   = strtolower(trim($applicant['gender'] ?? ''));
    $isFemale      = str_starts_with($genderLower, 'p')
                     || in_array(strtolower($maritalStatus), ['perawan', 'janda']);
    $isMale        = !$isFemale && $maritalStatus !== '';
@endphp

    <div class="form-ref">
        Lampiran V Keputusan Dirjen Bimas Islam<br>
        Nomor 473 Tahun 2020 tentang<br>
        Petunjuk Teknis Pelaksanaan Pencatatan Nikah<br><br>
        Model N1
    </div>

    <div class="kop-block">
        <table>
            <tr><td class="lbl">KANTOR DESA/KELURAHAN</td><td class="sep">:</td><td>{{ $village['name'] ?? '' }}</td></tr>
            <tr><td class="lbl">KECAMATAN</td><td class="sep">:</td><td>{{ $village['subdistrict'] ?? '' }}</td></tr>
            <tr><td class="lbl">KABUPATEN/KOTA</td><td class="sep">:</td><td>{{ $village['regency'] ?? '' }}</td></tr>
        </table>
    </div>

    <div class="title">PENGANTAR NIKAH</div>
    <div class="number">Nomor: {{ $number ?? '' }}</div>

    <p>Yang bertanda tangan di bawah ini menjelaskan dengan sesungguhnya bahwa :</p>
    <table class="data">
        <tr><td class="no">1.</td><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $applicant['name'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">Jenis Kelamin</td><td class="sep">:</td><td class="val">{{ $applicant['gender'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $applicant['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $applicant['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $applicant['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $applicant['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8.</td><td class="lbl">Pendidikan Terakhir</td><td class="sep">:</td><td class="val">{{ $applicant['last_education'] ?? '' }}</td></tr>
        <tr><td class="no">9.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $applicant['address'] ?? '' }}</td></tr>
        <tr>
            <td class="no">10.</td>
            <td class="lbl">Status pernikahan</td>
            <td class="sep">:</td>
            <td class="val"></td>
        </tr>
        <tr class="status-row">
            <td></td>
            <td class="lbl" style="padding-left: 16pt;">a. Laki-laki : Jejaka, Duda, atau beristri ke .....</td>
            <td class="sep">:</td>
            <td class="val">{{ $isMale ? $maritalStatus : '' }}</td>
        </tr>
        <tr class="status-row">
            <td></td>
            <td class="lbl" style="padding-left: 16pt;">b. Perempuan : Perawan, atau janda</td>
            <td class="sep">:</td>
            <td class="val">{{ $isFemale ? $maritalStatus : '' }}</td>
        </tr>
        <tr><td class="no">11.</td><td class="lbl">Nama istri/suami terdahulu</td><td class="sep">:</td><td class="val">{{ $applicant['previous_spouse_name'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">Adalah benar anak dari perkawinan seorang pria :</p>
    <table class="data">
        <tr><td class="lbl">&bull; Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $father['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; NIK</td><td class="sep">:</td><td class="val">{{ $father['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $father['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $father['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Agama</td><td class="sep">:</td><td class="val">{{ $father['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Pekerjaan</td><td class="sep">:</td><td class="val">{{ $father['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Alamat</td><td class="sep">:</td><td class="val">{{ $father['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">dengan seorang wanita :</p>
    <table class="data">
        <tr><td class="lbl">&bull; Nama lengkap dan alias</td><td class="sep">:</td><td class="val">{{ $mother['full_name_alias'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; NIK</td><td class="sep">:</td><td class="val">{{ $mother['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $mother['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $mother['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Agama</td><td class="sep">:</td><td class="val">{{ $mother['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Pekerjaan</td><td class="sep">:</td><td class="val">{{ $mother['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">&bull; Alamat</td><td class="sep">:</td><td class="val">{{ $mother['address'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">
        Demikian, Surat Pengantar ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan
        sebagaimana mestinya.
    </p>

    <div class="signature-block">
        <div>{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>
        <div>{{ $signature['position'] ?? 'Kamituwa' }}</div>
        <div style="height: 34pt;">&nbsp;</div>
        <div>{{ $signature['name'] ?? '' }}</div>
    </div>

</body>
</html>