{{--
    Surat Keterangan Penghasilan.
    Isi kolom letter_types.blade_view dengan: letters.surat-keterangan-penghasilan

    Data yang dipakai (dibentuk oleh LetterPdfService::viewData):
      $nomor, $signer, $applicant (name, birth, nik, marital_status, gender,
      religion, occupation, address), $income, $purpose, $signature
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Penghasilan')
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

    .blank-fixed {
        display: inline-block; min-width: 150pt;
        border-bottom: 1px dashed #000; padding: 0 4pt;
    }

    table.purpose-row { margin-left: 0; width: 457pt; margin-top: 10pt; }
    table.purpose-row td { padding: 0; vertical-align: bottom; }
    table.purpose-row td.purpose-lbl { width: 1%; white-space: nowrap; padding-right: 4pt; }
    table.purpose-row td.purpose-val { border-bottom: 1px dashed #000; padding-left: 4pt; }

    .purpose-cont { margin-left: 0; width: 457pt; border-bottom: 1px dashed #000; height: 13pt; }

    p.gap-top-lg { margin-top: 14pt; margin-bottom: 3pt; }
    p.gap-top-xl { margin-top: 16pt; margin-bottom: 3pt; }
    p.gap-top { margin-top: 5pt; margin-bottom: 3pt; }
       (disamakan dengan surat lain) */
    p.gap-keterangan { margin-top: 6pt; margin-bottom: 3pt; }
    p { margin: 3pt 0; }
    .no-break, .signature, .ttd { page-break-inside: avoid; }
@endsection

@section('content')
    <div class="judul">SURAT KETERANGAN PENGHASILAN</div>
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
            <td class="lbl">Status Perkawinan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['marital_status'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['gender'] }}</td>
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

    <p class="gap-top-xl">Adalah Warga Dengan penghasilan <span class="blank-fixed">{{ $income }}</span> per bulan</p>
    <table class="purpose-row">
        <tr>
            <td class="purpose-lbl">Surat Keterangan ini dipergunakan untuk</td>
            <td class="purpose-val">{{ $purpose }}</td>
        </tr>
    </table>
    <div class="purpose-cont">&nbsp;</div>

    <p class="gap-keterangan">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection