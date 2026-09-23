@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Jalan')
@section('date_long', '1')

@section('extra_css')
    body { font-family: Times, serif; font-size: 12pt; line-height: 1.05; }
    .kop-1, .kop-2, .kop-info { font-family: Helvetica, Arial, sans-serif; }
    .kop-3, .judul { font-family: Times, serif; }
    .kop-3 { font-size: 17.5pt; }
    table.data { margin-left: 74pt; width: 383pt; }
    table.data td { padding: 0.6pt 0; }
    td.lbl { width: 112pt; }
    td.sep { width: 11pt; }
   
    td.nik-val span { display: inline-block; box-sizing: border-box; vertical-align: bottom; }
    td.nik-val .nik-num {
        width: 92pt; padding-left: 2pt; height: 12pt; overflow: hidden;
    }

    table.purpose { margin-left: 0; width: 457pt; }
    table.purpose td.val { height: 14pt; vertical-align: bottom; border-bottom: 1px dashed #000; }

    p.gap-top-lg { margin-top: 14pt; margin-bottom: 2pt; }
    p.gap-top { margin-top: 3pt; margin-bottom: 2pt; }
       (disamakan dengan surat lain) */
    p.gap-keterangan { margin-top: 6pt; margin-bottom: 2pt; }
    p { margin: 2pt 0; }
    .no-break, .signature, .ttd { page-break-inside: avoid; }
@endsection

@section('content')
    <div class="judul">SURAT KETERANGAN JALAN</div>
    <div class="nomor">NOMOR : {{ $nomor }}</div>

    <p class="gap-top-lg">Yang bertanda tangan dibawah ini</p>
    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $signer['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jabatan</td>
            <td class="sep">:</td>
            <td class="val">{{ $signer['position'] }}</td>
        </tr>
    </table>

    <p class="gap-top">Dengan ini menerangkan bahwa</p>
    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat/Tgl. Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">NIK</td>
            <td class="sep">:</td>
            <td class="val nik-val"><span class="nik-num">{{ $applicant['nik'] }}</span><span class="kk-lbl">NO KK</span><span class="kk-sep">:</span><span class="kk-num">{{ $applicant['kk_number'] }}</span></td>
        </tr>
    </table>
    <table class="data">
        <tr>
            <td class="lbl">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['gender'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Status Perkawinan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['marital_status'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Agama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['religion'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['occupation'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
    </table>
    <table class="data">
        <tr>
            <td class="lbl">Nomer Telepon</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['phone'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Pergi ke</td>
            <td class="sep">:</td>
            <td class="val">{{ $destination }}</td>
        </tr>
    </table>

    <p class="gap-keterangan justify">
        Bahwa yang bersangkutan merupakan warga kami sesuai bukti kependudukan yang terdata pada
        Sistem Informasi Administrasi Kependudukan Kabupaten Sleman, Daerah Istimewa Yogyakarta dengan
    </p>
    <p class="gap-top">
        Adapun maksud dan tujuan saudara tersebut diatas ke tempat tersebut untuk :
    </p>
    <table class="data purpose">
        <tr>
            <td class="val" style="width: 100%;">{{ $purpose }}</td>
        </tr>
    </table>

    <p class="gap-top justify">
        berhubung maksud yang bersangkutan, mohon yang berwenang memberikan bantuan serta Fasilitas
        seperlunya
    </p>
    <p>Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection