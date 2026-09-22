{{--
    Surat Keterangan Keramaian.
    Isi kolom letter_types.blade_view dengan: letters.surat-keterangan-keramaian

    Data yang dipakai (dibentuk oleh LetterPdfService::viewData):
      $nomor, $signer, $applicant, $destination, $purpose, $event,
      $responsible_person, $signature
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Keramaian')
@section('date_long', '1')

@section('extra_css')
    body { font-family: Times, serif; font-size: 12pt; line-height: 1.15; }
    .kop-1, .kop-2, .kop-info { font-family: Helvetica, Arial, sans-serif; }
    .kop-3, .judul { font-family: Times, serif; }
    .kop-3 { font-size: 17.5pt; }

    table.data { margin-left: 74pt; width: 383pt; }
    table.data td { padding: 1pt 0; }
    td.lbl { width: 146pt; }
    td.sep { width: 10pt; white-space: nowrap; }
    td.val { word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; }
    table.purpose { margin-left: 0; width: 457pt; }
    table.purpose td.lbl { width: 215pt; white-space: nowrap; }
    table.purpose td.sep { width: 0; }

    p.gap-top-lg { margin-top: 7pt; margin-bottom: 3pt; }
    p.gap-top { margin-top: 4pt; margin-bottom: 3pt; }
    p { margin: 3pt 0; }
    .no-break, .signature, .ttd { page-break-inside: avoid; }
@endsection

@section('content')
    <div class="judul">SURAT KETERANGAN KERAMAIAN</div>
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
        <tr>
            <td class="lbl">Pergi ke</td>
            <td class="sep">:</td>
            <td class="val">{{ $destination }}</td>
        </tr>
        <tr>
            <td class="lbl">Keperluan</td>
            <td class="sep">:</td>
            <td class="val">{{ $purpose }}</td>
        </tr>
    </table>

    <p class="gap-top">Untuk Kegiatan/Acara</p>
    <table class="data event">
        <tr>
            <td class="lbl">Pada Hari/Tanggal</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['date'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jam</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['time'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['place'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Peserta</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['participants'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tujuan</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['objective'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Penanggung Jawab</td>
            <td class="sep">:</td>
            <td class="val">{{ $responsible_person }}</td>
        </tr>
    </table>

    <p class="gap-top">
        Berhubung maksud yang bersangkutan, mohon yang berwenang memberikan bantuan serta fasilitas seperlunya.
    </p>
    <p>Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection