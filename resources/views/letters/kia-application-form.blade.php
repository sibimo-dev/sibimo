<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Formulir Permohonan Penerbitan KIA</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; line-height: 1.2; color: #000; margin: 0; }

        table.kia-kop { width: 100%; border-collapse: collapse; }
        table.kia-kop td { padding: 0; vertical-align: middle; }
        td.kia-logo { width: 55pt; }
        td.kia-logo img { width: 48pt; }
        td.kia-kop-text { text-align: center; }
        .kia-k1, .kia-k2 { font-size: 11.5pt; font-weight: bold; }
        .kia-k3 { font-size: 8.5pt; }
        .kia-line { border-bottom: 2px solid #000; margin-top: 3pt; }

        .kia-judul { text-align: center; font-weight: bold; margin-top: 5pt; font-size: 10.5pt; }

        /* Setiap baris form: label mengalir normal (menentukan tinggi baris),
           titik dua & garis isian dipatok absolut supaya presisi, tidak tergantung tabel */
        .kia-row { position: relative; padding: 2pt 0; font-size: 9.5pt; }
        .kia-row .kia-lbl { display: inline-block; width: 170pt; white-space: nowrap; }
        .kia-row .kia-lbl.indent { padding-left: 14pt; }
        .kia-row .kia-colon { position: absolute; left: 170pt; top: 2pt; }
        .kia-row .kia-val { position: absolute; left: 180pt; top: 2pt; height: 12pt; padding-left: 4pt;
            border-bottom: 1px dotted #000; }
        .kia-row .kia-val.full  { width: 314pt; }
        .kia-row .kia-val.short { width: 226pt; }
        .kia-row .kia-val.gender{ width: 136pt; }

        .kia-row .kia-lbl2   { position: absolute; left: 330pt; top: 2pt; white-space: nowrap; }
        .kia-row .kia-colon2 { position: absolute; left: 405pt; top: 2pt; }
        .kia-row .kia-val2   { position: absolute; left: 413pt; top: 2pt; width: 81pt; height: 12pt;
            padding-left: 4pt; border-bottom: 1px dotted #000; }

        .kia-photo-wrap { position: relative; }
        .kia-photo-box {
            position: absolute;
            top: 20pt;
            right: 0;
            width: 78pt;
            height: 140pt;
            border: 1px solid #000;
            background: #fff;
            z-index: 2;
        }

        /* Blok TTD Pemohon: tabel 2 kolom, supaya "Sleman," & "Pemohon" sejajar kiri,
           dan tanggal & NIK sejajar di kolom kanan */
        .kia-sign-box { width: 160pt; margin-left: auto; margin-top: 8pt; font-size: 9.5pt; text-align: left; }
        .kia-sign-box .s-space { height: 28pt; }
        .kia-cut { border-top: 1px dashed #000; text-align: center; font-style: italic; font-size: 8pt; margin-top: 8pt; padding-top: 2pt; }

        .kia-receipt-title { text-align: center; font-weight: bold; margin-top: 4pt; font-size: 10pt; }
        table.kia-receipt { width: 100%; border-collapse: collapse; }
        table.kia-receipt td { padding: 2pt 4pt 2pt 0; vertical-align: bottom; font-size: 9.5pt; }
        .kia-sign-box2 .s-space { height: 28pt; }
        .kia-r-row { position: relative; padding: 2pt 0; font-size: 9.5pt; }
        .kia-r-row .kia-lbl { display: inline-block; width: 70pt; white-space: nowrap; }
        .kia-r-row .kia-colon { position: absolute; left: 70pt; top: 2pt; }
        .kia-r-row .kia-val { position: absolute; left: 80pt; top: 2pt; height: 12pt; padding-left: 4pt;
            border-bottom: 1px dotted #000; }
        .kia-r-row .kia-val.narrow { width: 270pt; }

        .kia-receipt-wrap { position: relative; margin-top: 4pt; }
        .kia-nomor-box2 {
            position: absolute;
            top: 0;
            right: 0;
            width: 115pt;
            height: 22pt;
            border: 1px solid #000;
            padding: 2pt 5pt;
            font-size: 9.5pt;
        }
        .kia-sign-box2 .s-space { height: 28pt; }
        .kia-sign-box2 .s-line { border-bottom: 1px solid #000; height: 12pt; width: 130pt; }
    </style>
</head>
<body>

<table class="kia-kop">
    <tr>
        <td class="kia-logo"><img src="{{ $logo }}" alt="Logo Kabupaten Sleman"></td>
        <td class="kia-kop-text">
            <div class="kia-k1">PEMERINTAH KABUPATEN SLEMAN</div>
            <div class="kia-k2">DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</div>
            <div class="kia-k3">Jalan KRT Pringgodiningrat No. 3 Beran, Tridadi, Sleman, DIY, 55511</div>
            <div class="kia-k3">Telepon (0274) 868362, Faksimile (0274) 868945</div>
            <div class="kia-k3">website : www.capil.slemankab.go.id, E-mail : dukcapil@slemankab.go.id</div>
        </td>
    </tr>
</table>
<div class="kia-line"></div>

<div class="kia-judul">
    FORMULIR PERMOHONAN PENERBITAN<br>
    KARTU IDENTITAS ANAK (KIA)
</div>

<div style="margin-top:5pt">
    <div class="kia-row"><span class="kia-lbl">NIK</span><span class="kia-colon">:</span><span class="kia-val full">{{ $kia['nik'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl">NAMA</span><span class="kia-colon">:</span><span class="kia-val full">{{ $kia['name'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl">TEMPAT/TANGGAL LAHIR</span><span class="kia-colon">:</span><span class="kia-val full">{{ $kia['birth'] }}</span></div>
</div>

<div class="kia-photo-wrap" style="margin-top:5pt">
    <div class="kia-photo-box">&nbsp;</div>

    <div class="kia-row">
        <span class="kia-lbl">JENIS KELAMIN</span><span class="kia-colon">:</span><span class="kia-val gender">{{ $kia['gender'] }}</span>
        <span class="kia-lbl2">GOL DARAH</span><span class="kia-colon2">:</span><span class="kia-val2">{{ $kia['blood_type'] }}</span>
    </div>
    <div class="kia-row"><span class="kia-lbl">NOMER KARTU KELUARGA</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['kk_number'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl">NAMA KEPALA KELUARGA</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['household_head'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl">NOMER AKTA KELAHIRAN</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['birth_cert_number'] }}</span></div>

    <div class="kia-row" style="margin-top:5pt"><span class="kia-lbl">AGAMA</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['religion'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl">KEWARGANEGARAAN</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['citizenship'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl">ALAMAT</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['address'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl indent">RT/RW</span><span class="kia-colon">:</span><span class="kia-val short">{{ collect([$kia['rt'] ?? null, $kia['rw'] ?? null])->filter()->implode(' / ') }}</span></div>
    <div class="kia-row"><span class="kia-lbl indent">KELURAHAN</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['village'] }}</span></div>
    <div class="kia-row"><span class="kia-lbl indent">KECAMATAN</span><span class="kia-colon">:</span><span class="kia-val short">{{ $kia['district'] }}</span></div>
</div>

<div class="kia-sign-box">
    <div>Sleman, {{ $kia['date'] }}</div>
    <div>Pemohon</div>
    <div class="s-space"></div>
    <div>{{ $kia['applicant_name'] ?? '' }}</div>
    <div>NIK {{ $kia['applicant_nik'] ?? '' }}</div>
</div>

<div class="kia-cut">&#9986; potong di sini</div>

<table class="kia-kop" style="margin-top:6pt">
    <tr>
        <td class="kia-logo"><img src="{{ $logo }}" alt="Logo Kabupaten Sleman"></td>
        <td class="kia-kop-text">
            <div class="kia-k1">PEMERINTAH KABUPATEN SLEMAN</div>
            <div class="kia-k2">DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</div>
            <div class="kia-k3">Jalan KRT Pringgodiningrat No. 3 Beran, Tridadi, Sleman, DIY, 55511</div>
            <div class="kia-k3">Telepon (0274) 868362, Faksimile (0274) 868945</div>
            <div class="kia-k3">website : www.capil.slemankab.go.id, E-mail : dukcapil@slemankab.go.id</div>
        </td>
    </tr>
</table>
<div class="kia-line"></div>

<div class="kia-receipt-title">TANDA BUKTI PENERIMAAN PERMOHONAN KIA</div>

<div class="kia-receipt-wrap">
    <div class="kia-nomor-box2">NOMOR :</div>

    <div class="kia-r-row"><span class="kia-lbl">NAMA</span><span class="kia-colon">:</span><span class="kia-val narrow">{{ $kia['name'] }}</span></div>
    <div class="kia-r-row"><span class="kia-lbl">NIK</span><span class="kia-colon">:</span><span class="kia-val narrow">{{ $kia['nik'] }}</span></div>
    <div class="kia-r-row"><span class="kia-lbl">ALAMAT</span><span class="kia-colon">:</span><span class="kia-val narrow">{{ $kia['address'] }}</span></div>
</div>

<table class="kia-receipt" style="margin-top:6pt">
    <tr>
        <td style="width:50%; vertical-align:top">Tgl Pengambilan</td>
        <td style="width:50%">
            <div class="kia-sign-box2">
                <div>Sleman, {{ $kia['date'] }}</div>
                <div>Petugas</div>
                <div class="s-space"></div>
                <div class="s-line">&nbsp;</div>
            </div>
        </td>
    </tr>
</table>

</body>
</html>
