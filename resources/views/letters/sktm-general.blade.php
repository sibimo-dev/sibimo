
@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Tidak Mampu')
@section('date_long', '1')

@section('extra_css')
    body { font-family: Times, serif; font-size: 12pt; line-height: 1.15; }
    .kop-1, .kop-2, .kop-info { font-family: Helvetica, Arial, sans-serif; }
    .kop-3, .judul { font-family: Times, serif; }
    .kop-3 { font-size: 17.5pt; }
    table.data { margin-left: 74pt; width: 383pt; }
    table.data td { padding: 1.4pt 0; }
    td.lbl { width: 112pt; }
    table.purpose { margin-left: 0; width: 457pt; }
    table.purpose td.lbl { width: 215pt; white-space: nowrap; }
    table.purpose td.sep { width: 0; }
@endsection

@section('content')
    <div class="judul">SURAT KETERANGAN TIDAK MAMPU</div>
    <div class="nomor">NOMOR : {{ $number }}</div>

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
            <td class="lbl">Penghasilan</td>
            <td class="sep">:</td>
            <td class="val">{{ $income ?? 'Rp. -' }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Kategori</td>
            <td class="sep">:</td>
            <td class="val">{{ $category }}</td>
        </tr>
        <tr>
            <td class="lbl">Nomer KKM/KRM</td>
            <td class="sep">:</td>
            <td class="val">{{ $kkm_number }}</td>
        </tr>
    </table>

    <table class="data purpose" style="margin-top: 16pt;">
        <tr>
            <td class="lbl">Surat Keterangan ini dipergunakan untuk</td>
            <td class="sep"></td>
            <td class="val">{{ $purpose }}</td>
        </tr>
        <tr>
            <td class="val" colspan="3">di {{ $destination }}</td>
        </tr>
    </table>

    <p style="margin-top: 9pt;">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection