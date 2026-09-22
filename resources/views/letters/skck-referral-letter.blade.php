@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Pengantar SKCK')

@section('content')

    <div class="judul">SURAT KETERANGAN</div>
    <div class="nomor">NOMOR : {{ $nomor }}</div>

    <p class="gap-top-lg">Yang bertanda tangan dibawah ini:</p>
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

    <p class="gap-top-lg">Dengan ini menerangkan bahwa:</p>
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
        <tr>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $form['occupation_note'] ?? null }}</td>
        </tr>
    </table>

    <p class="gap-top">
        Berhubung maksud yang bersangkutan, mohon yang berwenang memberikan bantuan serta fasilitas
        seperlunya.
    </p>

    <p class="gap-top">
        Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.
    </p>

@endsection