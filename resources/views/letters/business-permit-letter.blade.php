{{--
    Surat Keterangan Usaha.
    Isi kolom letter_types.blade_view dengan: letters.surat-keterangan-usaha

    Data yang dipakai (dibentuk oleh LetterPdfService::viewData):
      $nomor, $signer, $applicant (name, birth, nik, marital_status, gender,
      occupation, address), $business (type, address), $purpose, $destination,
      $signature
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Usaha')
@section('date_long', '1')

@section('extra_css')
    body { font-family: Times, serif; font-size: 12pt; line-height: 1.12; }
    .kop-1, .kop-2, .kop-info { font-family: Helvetica, Arial, sans-serif; }
    .kop-3, .judul { font-family: Times, serif; }
    .kop-3 { font-size: 17.5pt; }
    table.data { margin-left: 74pt; width: 383pt; }
    table.data td { padding: 1.1pt 0; }
    td.lbl { width: 130pt; }
    td.sep { width: 11pt; }
    td.val { word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; }

    table.purpose-row { margin-left: 0; width: 457pt; margin-top: 10pt; }
    table.purpose-row + table.purpose-row { margin-top: 0; }
    table.purpose-row td { padding: 1pt 0; vertical-align: bottom; }
    table.purpose-row td.purpose-lbl { width: 1%; white-space: nowrap; padding-right: 4pt; }
    table.purpose-row td.purpose-val { padding-left: 4pt; }

    p.gap-top-lg { margin-top: 14pt; margin-bottom: 3pt; }
    p.gap-top { margin-top: 5pt; margin-bottom: 3pt; }
    p.gap-keterangan { margin-top: 6pt; margin-bottom: 3pt; }
    p { margin: 3pt 0; }
    .no-break, .signature, .ttd { page-break-inside: avoid; }
@endsection

@section('content')
    <div class="judul">SURAT KETERANGAN USAHA</div>
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
            <td class="val">{{ $applicant['nik'] }}</td>
        </tr>
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
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['occupation'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
        <tr>
            <td class="lbl">&nbsp;</td>
            <td class="sep">&nbsp;</td>
            <td class="val">&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Usaha</td>
            <td class="sep">:</td>
            <td class="val">{{ $business['type'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat Usaha</td>
            <td class="sep">:</td>
            <td class="val">{{ $business['address'] }}</td>
        </tr>
    </table>

    <table class="purpose-row">
        <tr>
            <td class="purpose-lbl">Surat Keterangan ini dipergunakan untuk</td>
            <td class="purpose-val">{{ $purpose }}</td>
        </tr>
    </table>
    <table class="purpose-row">
        <tr>
            <td class="purpose-lbl">di</td>
            <td class="purpose-val">{{ $destination }}</td>
        </tr>
    </table>

    <p class="gap-keterangan">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection