@extends('letters.layouts.base')

@section('title', 'Surat Keterangan')

@section('content')
@php
    // Keperluan dipecah per baris (maks. ~44 karakter); tiap baris punya garis titik-titik sendiri.
    $words = [];
    foreach (preg_split('/\s+/', 'Mohon Akta Kelahiran Untuk Anak Bernama', -1, PREG_SPLIT_NO_EMPTY) as $w) {
        $words[] = [$w, false];
    }
    foreach (preg_split('/\s+/', trim((string) ($birth['child']['name'] ?? '')), -1, PREG_SPLIT_NO_EMPTY) as $w) {
        $words[] = [$w, true];
    }
    $purposeLines = [];
    $current = [];
    $length = 0;
    foreach ($words as $word) {
        $add = mb_strlen($word[0]) + ($current ? 1 : 0);
        if ($current && $length + $add > 44) {
            $purposeLines[] = $current;
            $current = [];
            $length = 0;
            $add = mb_strlen($word[0]);
        }
        $current[] = $word;
        $length += $add;
    }
    if ($current) {
        $purposeLines[] = $current;
    }
@endphp
    <div class="judul">SURAT KETERANGAN</div>
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
            <td class="lbl">Tempat/Tgl.Lahir</td>
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
            <td class="val">Dinas Kependudukan Dan Pencatatan Sipil Sleman</td>
        </tr>
        @foreach ($purposeLines as $i => $line)
        <tr>
            <td class="lbl">{{ $i === 0 ? 'Keperluan' : '' }}</td>
            <td class="sep">{{ $i === 0 ? ':' : '' }}</td>
            <td class="val">@foreach ($line as [$word, $bold])@if ($bold)<strong>{{ $word }}</strong>@else{{ $word }}@endif{{ ! $loop->last ? ' ' : '' }}@endforeach</td>
        </tr>
        @endforeach
    </table>

    <p class="gap-top">berhubung maksud yang bersangkutan, mohon yang berwenang memberikan bantuan serta Fasilitas seperlunya.</p>

    <p class="gap-top">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection