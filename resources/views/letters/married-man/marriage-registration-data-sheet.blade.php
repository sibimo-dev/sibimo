{{--
    Data Isian Pendaftaran Nikah (lembar rekap, bukan surat bertanda tangan).
    Isi kolom letter_types.blade_view dengan: letters.marriage-registration-data-sheet

    Data: $village, $ceremony, $groom, $groomFather, $groomMother,
          $bride, $brideFather, $brideMother
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Data Isian Pendaftaran Nikah</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 8pt; line-height: 1.1; color:#000; }
        p { margin: 0; }
        .title { text-align:center; font-weight:bold; margin-bottom: 15pt; }
        .section-title { font-weight:bold; margin-top: 7pt; margin-bottom: 4pt; }
        table.data { width:100%; border-collapse:collapse; }
        table.data td { padding: 0 0; vertical-align:top; }
        td.lbl { width: 160pt; }
        td.sep { width: 10pt; }
        td.val { padding-left: 3pt; }
    </style>
</head>
<body>

    <div class="title">DATA ISIAN PENDAFTARAN NIKAH</div>

    <div class="section-title">DATA DESA</div>
    <table class="data">
        <tr><td class="lbl">DESA</td><td class="sep">:</td><td class="val">{{ $village['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">KECAMATAN</td><td class="sep">:</td><td class="val">{{ $village['subdistrict'] ?? '' }}</td></tr>
        <tr><td class="lbl">KABUPATEN/KOTA</td><td class="sep">:</td><td class="val">{{ $village['regency'] ?? '' }}</td></tr>
        <tr><td class="lbl">NO. SURAT</td><td class="sep">:</td><td class="val">{{ $village['letter_number'] ?? '' }}</td></tr>
        <tr><td class="lbl">TANGGAL SURAT</td><td class="sep">:</td><td class="val">{{ $village['letter_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">NAMA KEPALA DESA/KALURAHAN</td><td class="sep">:</td><td class="val">{{ $village['head_name'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA AKAD NIKAH</div>
    <table class="data">
        <tr><td class="lbl">HARI AKAD</td><td class="sep">:</td><td class="val">{{ $ceremony['day'] ?? '' }}</td></tr>
        <tr><td class="lbl">TANGGAL AKAD</td><td class="sep">:</td><td class="val">{{ $ceremony['date'] ?? '' }}</td></tr>
        <tr><td class="lbl">JAM AKAD</td><td class="sep">:</td><td class="val">{{ $ceremony['time'] ?? '' }}</td></tr>
        <tr><td class="lbl">TEMPAT AKAD</td><td class="sep">:</td><td class="val">{{ $ceremony['place'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA CATIN PUTRA</div>
    <table class="data">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $groom['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $groom['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $groom['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $groom['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $groom['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $groom['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pendidikan Terakhir</td><td class="sep">:</td><td class="val">{{ $groom['last_education'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $groom['address'] ?? '' }}</td></tr>
        <tr><td class="lbl">Status</td><td class="sep">:</td><td class="val">{{ $groom['status'] ?? '' }}</td></tr>
        <tr><td class="lbl">Jika Duda, Nama Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_name'] ?? '' }}</td></tr>
        <tr><td class="lbl">Jika Duda MATI, Data Istri Terdahulu</td><td class="sep">:</td><td class="val">&nbsp;</td></tr>
        <tr><td class="lbl">Nama Istri/Suami/Nama Orangtua</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_parent_name'] ?? '' }}</td></tr>
        <tr><td class="lbl">Bin Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_bin'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">TTL Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat Dunia Istri Terdahulu</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_address'] ?? '' }}</td></tr>
        <tr><td class="lbl">Meninggal Dunia Pada</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_death_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Meninggal Di</td><td class="sep">:</td><td class="val">{{ $groom['previous_spouse_death_place'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA AYAH CATIN PUTRA</div>
    <table class="data">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $groomFather['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">BIN</td><td class="sep">:</td><td class="val">{{ $groomFather['bin'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $groomFather['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $groomFather['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $groomFather['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $groomFather['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $groomFather['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $groomFather['address'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA IBU CATIN PUTRA</div>
    <table class="data">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $groomMother['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">BINTI</td><td class="sep">:</td><td class="val">{{ $groomMother['binti'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $groomMother['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $groomMother['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $groomMother['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $groomMother['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $groomMother['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $groomMother['address'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA CATIN PUTRI</div>
    <table class="data">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $bride['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">BINTI</td><td class="sep">:</td><td class="val">{{ $bride['binti'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $bride['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $bride['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $bride['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $bride['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $bride['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pendidikan Terakhir</td><td class="sep">:</td><td class="val">{{ $bride['last_education'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $bride['address'] ?? '' }}</td></tr>
        <tr><td class="lbl">Status</td><td class="sep">:</td><td class="val">{{ $bride['status'] ?? '' }}</td></tr>
        <tr><td class="lbl">Jika Janda, Nama Suami Terdahulu</td><td class="sep">:</td><td class="val">{{ $bride['previous_spouse_name'] ?? '' }}</td></tr>
        <tr><td class="lbl">Bin Suami Terdahulu</td><td class="sep">:</td><td class="val">{{ $bride['previous_spouse_bin'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK Suami Terdahulu</td><td class="sep">:</td><td class="val">{{ $bride['previous_spouse_nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Meninggal Dunia Pada</td><td class="sep">:</td><td class="val">{{ $bride['previous_spouse_death_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Meninggal Di</td><td class="sep">:</td><td class="val">{{ $bride['previous_spouse_death_place'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA AYAH CATIN PUTRI</div>
    <table class="data">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $brideFather['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">BIN</td><td class="sep">:</td><td class="val">{{ $brideFather['bin'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $brideFather['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $brideFather['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $brideFather['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $brideFather['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $brideFather['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $brideFather['address'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">DATA IBU CATIN PUTRI</div>
    <table class="data">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $brideMother['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">BINTI</td><td class="sep">:</td><td class="val">{{ $brideMother['binti'] ?? '' }}</td></tr>
        <tr><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $brideMother['nik'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Tanggal Lahir</td><td class="sep">:</td><td class="val">{{ $brideMother['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="lbl">Kewarganegaraan</td><td class="sep">:</td><td class="val">{{ $brideMother['nationality'] ?? '' }}</td></tr>
        <tr><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $brideMother['religion'] ?? '' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $brideMother['occupation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $brideMother['address'] ?? '' }}</td></tr>
    </table>

</body>
</html>